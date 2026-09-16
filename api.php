<?php


/**
 * SECRET GATE - ZERO-KNOWLEDGE EPHEMERAL MESSAGING ENGINE
 *
 * @package     SecretGate
 * @version     1.0.0-Release
 * 
 * @author      Sasiru Mindaka <info@secretgate.site>
 * @copyright   2026 Sasiru Mindaka
 * 
 * @license     AGPL-3.0-or-later <https://www.gnu.org/licenses/agpl-3.0.html>
 * @link        https://github.com/sasiru-mindaka/Secret-Gate
 * @see         https://secretgate.site
 */


// CONFIG LOAD – keep deployment settings outside the API file
require_once __DIR__ . '/config.php';

// HOST NORMALIZATION – stop www/non-www from being treated as different hosts
function api_normalize_host($host) {
    $host = strtolower(trim((string)$host));
    return preg_replace('/^www\./', '', $host);
}

// REQUEST ORIGIN CHECK – block cross-site calls before they reach the API
$apiHost = $_SERVER['HTTP_HOST'] ?? '';
$apiRefererHost = isset($_SERVER['HTTP_REFERER'])
    ? (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ?? '')
    : '';
$apiOriginHost = isset($_SERVER['HTTP_ORIGIN'])
    ? (parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST) ?? '')
    : '';

$apiRefererOk = $apiRefererHost !== ''
    && api_normalize_host($apiRefererHost) === api_normalize_host($apiHost);
$apiOriginOk = $apiOriginHost !== ''
    && api_normalize_host($apiOriginHost) === api_normalize_host($apiHost);

$apiForeignReferer = $apiRefererHost !== '' && !$apiRefererOk;
$apiForeignOrigin  = $apiOriginHost !== '' && !$apiOriginOk;

// AJAX REQUEST CHECK – stop direct browser hits to the endpoint
$isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// FORBIDDEN RESPONSE – fail fast when the caller is not the app
if (!$isAjaxRequest || $apiForeignReferer || $apiForeignOrigin) {
    http_response_code(403);
    if (file_exists(__DIR__ . '/403.php')) {
        include __DIR__ . '/403.php';
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'Forbidden']);
    }
    exit;
}

// RESTRICT ACCESS – only allow requests from our own domain
$host = $_SERVER['HTTP_HOST'] ?? '';
$refererHost = isset($_SERVER['HTTP_REFERER']) ? (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ?? '') : '';
$originHost = isset($_SERVER['HTTP_ORIGIN']) ? (parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST) ?? '') : '';

$refererOk = $refererHost !== '' && api_normalize_host($refererHost) === api_normalize_host($host);
$originOk  = $originHost !== '' && api_normalize_host($originHost) === api_normalize_host($host);

$req_action_precheck = $_GET['action'] ?? '';
$readonly_actions_precheck = defined('API_READONLY_GET_ACTIONS') ? API_READONLY_GET_ACTIONS : ['get_csrf', 'get_public_key', 'get_messages'];
$is_allowed_readonly_get_precheck = $_SERVER['REQUEST_METHOD'] === 'GET' && in_array($req_action_precheck, $readonly_actions_precheck, true);

// METHOD RESTRICTION – only expose read-only GETs and normal writes
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'OPTIONS' && !$is_allowed_readonly_get_precheck) {
    http_response_code(403);
    include __DIR__ . '/403.php';
    exit();
}

// CORS HEADERS – let the app talk to the API without opening it to other sites
header('Content-Type: application/json');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$originHostOnly = $origin !== '' ? (parse_url($origin, PHP_URL_HOST) ?? '') : '';
if ($originHostOnly !== '' && api_normalize_host($originHostOnly) === api_normalize_host($host)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: ' . (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $host);
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// OPTIONS PREFLIGHT – answer browser preflight before touching data
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// DATABASE CONNECTION – needed before any data action
try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('api.php: DB connection failed — ' . $e->getMessage());
    die(json_encode(['success' => false, 'error' => 'Something went wrong. Please refresh the page and try again.']));
}

// CLEANUP TRIGGER – run at most once per interval rather than on ~5% of every
// request, so a request flood can't be used to force repeated table scans.
// Falls back to the old probabilistic trigger if APCu isn't available.
$cleanup_min_interval = 300; // seconds
if (function_exists('apcu_fetch')) {
    $last_cleanup = apcu_fetch('sb_last_cleanup');
    if ($last_cleanup === false || (time() - $last_cleanup) >= $cleanup_min_interval) {
        apcu_store('sb_last_cleanup', time());
        cleanup_expired_entities($conn);
    }
} elseif (mt_rand(1, 100) <= 5) {
    cleanup_expired_entities($conn);
}

// ACTION INIT – single place to choose the requested operation
$action = $_REQUEST['action'] ?? '';

// CSRF TOKEN GENERATION – give the app a token for POST actions. Skipped for
// read-only GET callers (e.g. get_public_key) so anonymous visitors don't get
// a session created just for reading data.
if (
    $action !== 'get_public_key' &&
    $action !== 'get_auth_salt' &&
    empty($_SESSION['csrf_token'])
) {
    session_reopen();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    session_commit();
}
$response = ['success' => false];

// CSRF VALIDATION – stop forged POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== '') {
    $client_csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $client_csrf)) {
        die(json_encode(['success' => false, 'error' => 'This page has been open for a while. Please refresh the page and try again.']));
    }
}

// TURNSTILE VERIFICATION – keep automated spam out of message sending
$turnstile_protected_actions = ['send_message'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, $turnstile_protected_actions, true)) {
    $turnstile_token = $_POST['cf_turnstile_response'] ?? '';
    if (empty($turnstile_token)) {
        die(json_encode(['success' => false, 'error' => 'Verification failed. Please refresh the page and try again.']));
    }

    $verify_data = [
        'secret'   => getenv('CF_TURNSTILE_SECRET_KEY'),
        'response' => $turnstile_token,
        'remoteip' => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'],
    ];

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($verify_data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
    ]);
    $turnstile_response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    $turnstile_result = $turnstile_response ? json_decode($turnstile_response, true) : null;

    if ($curl_error || empty($turnstile_result['success'])) {
        error_log('Turnstile verification failed: ' . ($curl_error ?: json_encode($turnstile_result['error-codes'] ?? [])));
        die(json_encode(['success' => false, 'error' => 'Verification failed. Please refresh the page and try again.']));
    }
}

// PAYLOAD SIZE LIMIT – cap encrypted message size to block abuse/storage bloat
if (!defined('MAX_ENCRYPTED_MESSAGE_BYTES')) {
    define('MAX_ENCRYPTED_MESSAGE_BYTES', 50 * 1024);
}

// INPUT SANITIZATION HELPER – trim only; HTML-escaping belongs at output time,
// not at storage time (storing escaped HTML causes double-encoding later).
function sanitize_input($data) {
    if (is_null($data)) return '';
    return trim((string)$data);
}

// PUBLIC ID VALIDATION HELPER – keep IDs in the expected format
function validate_public_id($id) {
    return preg_match('/^[a-f0-9]{16}$/', $id) === 1;
}

// EXPIRED ENTITY CLEANUP – remove data that should no longer exist
function cleanup_expired_entities(PDO $conn): void {
    $conn->exec("DELETE FROM sb_messages WHERE expires_at IS NOT NULL AND expires_at <= NOW()");

    $conn->exec(
        "DELETE FROM sb_links
         WHERE account_ttl_seconds IS NOT NULL
           AND account_ttl_seconds > 0
           AND last_accessed_at <= DATE_SUB(NOW(), INTERVAL account_ttl_seconds SECOND)"
    );
}

// RATE LIMITING – reduce abuse and accidental request floods
if (function_exists('rate_limit_check')) {
    $ip = function_exists('getClientIp') ? getClientIp() : ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    if (!rate_limit_check($ip, 60, 60)) {
        die(json_encode(['success' => false, 'error' => "You're going a bit too fast. Please wait a moment and try again."]));
    }
}

// ACTION SWITCH – route each request to its handler
switch ($action) {

    // GET CSRF – safe to serve without touching storage
    case 'get_csrf':
        echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
        exit;

    // CREATE LINK – register the receiver's public key and password hash
    case 'create_link':
        $public_id = sanitize_input($_POST['public_id'] ?? '');
        $public_key = $_POST['public_key'] ?? '';
        $auth_key  = $_POST['auth_key']  ?? '';
        $auth_salt = $_POST['auth_salt'] ?? '';

        if (empty($public_id) || empty($public_key) || empty($auth_key) || empty($auth_salt)) {
            $response['error'] = 'Missing required fields';
            break;
        }
        if (!validate_public_id($public_id)) {
            $response['error'] = 'Invalid public ID format';
            break;
        }
        if (!preg_match('/^[a-f0-9]{64}$/', $auth_key)) {
            $response['error'] = 'Invalid auth key format';
            break;
        }
        if (strlen($auth_salt) > 32) {
            $response['error'] = 'Invalid auth salt';
            break;
        }
        // PUBLIC KEY FORMAT – reject anything that is not a usable JWK
        if (!json_decode($public_key)) {
            $response['error'] = 'Invalid public key format';
            break;
        }

        // AUTH KEY HASH – server never sees the plaintext password
        $hash = password_hash($auth_key, PASSWORD_ARGON2ID);

        try {
            $stmt = $conn->prepare("INSERT INTO sb_links (public_id, public_key, password_hash, auth_salt) VALUES (?, ?, ?, ?)");
            $stmt->execute([$public_id, $public_key, $hash, $auth_salt]);
            $response['success'] = true;
        } catch (PDOException $e) {
            error_log('api.php: create_link failed — ' . $e->getMessage());
            $response['error'] = 'Could not create link. ID may already exist.';
        }
        break;

    // GET PUBLIC KEY – sender needs it before encrypting
    case 'get_public_key':
        $public_id = sanitize_input($_GET['public_id'] ?? '');
        if (empty($public_id) || !validate_public_id($public_id)) {
            $response['error'] = 'Invalid or missing public_id';
            break;
        }
        // ENUMERATION THROTTLE – slow down brute-forcing of public IDs
        if (function_exists('rate_limit_check') && !rate_limit_check('enum_' . getClientIp(), 30, 60)) {
            $response['error'] = 'Too many requests. Please slow down.';
            break;
        }
        $stmt = $conn->prepare("SELECT public_key FROM sb_links WHERE public_id = ?");
        $stmt->execute([$public_id]);
        if ($row = $stmt->fetch()) {
            $response['success'] = true;
            $response['public_key'] = $row['public_key'];
        } else {
            $response['error'] = 'Receiver not found';
        }
        break;

    // GET AUTH SALT – Provides the salt required for the client to derive the auth_key.
    case 'get_auth_salt':
        $public_id = sanitize_input($_GET['public_id'] ?? '');
        if (empty($public_id) || !validate_public_id($public_id)) {
            $response['error'] = 'Invalid or missing public_id';
            break;
        }
        // ENUMERATION THROTTLE – slow down brute-forcing of public IDs
        if (function_exists('rate_limit_check') && !rate_limit_check('enum_' . getClientIp(), 30, 60)) {
            $response['error'] = 'Too many requests. Please slow down.';
            break;
        }
        $stmt = $conn->prepare("SELECT auth_salt FROM sb_links WHERE public_id = ?");
        $stmt->execute([$public_id]);
        if ($row = $stmt->fetch()) {
            $response['success'] = true;
            $response['auth_salt'] = $row['auth_salt'];
        } else {
            $response['error'] = 'Link not found';
        }
        break;


    // SEND MESSAGE – store only ciphertext for the receiver
    case 'send_message':
        $target_id = sanitize_input($_POST['public_id'] ?? '');
        $encrypted_message = $_POST['encrypted_message'] ?? '';

        if (empty($target_id) || empty($encrypted_message)) {
            $response['error'] = 'Missing fields';
            break;
        }
        if (!validate_public_id($target_id)) {
            $response['error'] = 'Invalid public ID';
            break;
        }

        // PAYLOAD SIZE CAP – reject oversized ciphertext (base64 encrypted blob)
        if (strlen($encrypted_message) > MAX_ENCRYPTED_MESSAGE_BYTES) {
            $response['error'] = 'Message too large';
            break;
        }

        $check = $conn->prepare("SELECT id, message_ttl_seconds FROM sb_links WHERE public_id = ?");
        $check->execute([$target_id]);
        $target_row = $check->fetch();
        if (!$target_row) {
            $response['error'] = 'Target link does not exist';
            break;
        }

        // MESSAGE TTL – inherit receiver's retention setting
        $message_ttl = (int)($target_row['message_ttl_seconds'] ?? 0);

        try {
            if ($message_ttl > 0) {
                $stmt = $conn->prepare("INSERT INTO sb_messages (link_public_id, encrypted_content, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))");
                $stmt->execute([$target_id, $encrypted_message, $message_ttl]);
            } else {
                $stmt = $conn->prepare("INSERT INTO sb_messages (link_public_id, encrypted_content) VALUES (?, ?)");
                $stmt->execute([$target_id, $encrypted_message]);
            }
            $response['success'] = true;
        } catch (PDOException $e) {
            error_log('api.php: send_message failed — ' . $e->getMessage());
            $response['error'] = 'Could not send message';
        }
        break;

    // GET MESSAGES – receiver pulls pending ciphertext
    case 'get_messages':
        $public_id = sanitize_input($_GET['public_id'] ?? '');
        if (empty($public_id) || !validate_public_id($public_id)) {
            $response['error'] = 'Invalid or missing public_id';
            break;
        }
        // PER-LINK THROTTLE – ciphertext can't be read without the key, but
        // repeated polling still leaks message counts/timing, so cap it.
        if (function_exists('rate_limit_check') && !rate_limit_check('getmsg_' . getClientIp() . '_' . $public_id, 20, 60)) {
            $response['error'] = 'Too many requests. Please slow down.';
            break;
        }

        // ACCESS TOUCH – account TTL depends on last read
        $touch = $conn->prepare("UPDATE sb_links SET last_accessed_at = NOW() WHERE public_id = ?");
        $touch->execute([$public_id]);

        $stmt = $conn->prepare("SELECT encrypted_content, created_at FROM sb_messages WHERE link_public_id = ? AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY created_at DESC");
        $stmt->execute([$public_id]);
        $messages = [];
        while ($row = $stmt->fetch()) {
            $messages[] = [
                'encrypted_content' => $row['encrypted_content'],
                'created_at' => $row['created_at']
            ];
        }
        $response['success'] = true;
        $response['messages'] = $messages;
        break;

    // VERIFY RESTORE – prove ownership before restoring access
    case 'verify_restore':
        $public_id = sanitize_input($_POST['public_id'] ?? '');
        $auth_key = $_POST['auth_key'] ?? '';
        if (empty($public_id) || empty($auth_key)) {
            $response['error'] = 'Missing fields';
            break;
        }
        if (!validate_public_id($public_id)) {
            $response['error'] = 'Invalid public ID';
            break;
        }
        $stmt = $conn->prepare("SELECT password_hash FROM sb_links WHERE public_id = ?");
        $stmt->execute([$public_id]);
        if ($row = $stmt->fetch()) {
            if (password_verify($auth_key, $row['password_hash'])) {
                // SESSION FIXATION GUARD – issue a fresh session ID and CSRF
                // token now that the caller has proven ownership of the link.
                session_reopen();
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                session_commit();

                $response['success'] = true;
                $response['csrf_token'] = $_SESSION['csrf_token'];
            } else {
                record_failed_auth_attempt(getClientIp());
                $response['error'] = 'Invalid password';
            }
        } else {
            $response['error'] = 'Link not found';
        }
        break;

    // GET AUTO DELETE SETTINGS – show current TTLs to the owner
    case 'get_auto_delete_settings':
        $public_id = sanitize_input($_GET['public_id'] ?? '');
        if (empty($public_id) || !validate_public_id($public_id)) {
            $response['error'] = 'Invalid or missing public_id';
            break;
        }
        $stmt = $conn->prepare("SELECT message_ttl_seconds, account_ttl_seconds FROM sb_links WHERE public_id = ?");
        $stmt->execute([$public_id]);
        if ($row = $stmt->fetch()) {
            $response['success'] = true;
            $response['message_ttl_seconds'] = $row['message_ttl_seconds'] !== null ? (int)$row['message_ttl_seconds'] : 0;
            $response['account_ttl_seconds'] = $row['account_ttl_seconds'] !== null ? (int)$row['account_ttl_seconds'] : 0;
        } else {
            $response['error'] = 'Link not found';
        }
        break;

    // SET AUTO DELETE SETTINGS – owner controls retention
    case 'set_auto_delete_settings':
        $public_id = sanitize_input($_POST['public_id'] ?? '');
        $auth_key  = $_POST['auth_key'] ?? '';
        $message_ttl_raw = $_POST['message_ttl_seconds'] ?? '0';
        $account_ttl_raw = $_POST['account_ttl_seconds'] ?? '0';

        if (empty($public_id) || !validate_public_id($public_id)) {
            $response['error'] = 'Invalid or missing public_id';
            break;
        }
        if (empty($auth_key)) {
            $response['error'] = 'Auth key required to change auto-delete settings';
            break;
        }
        if (!ctype_digit((string)$message_ttl_raw) || !ctype_digit((string)$account_ttl_raw)) {
            $response['error'] = 'Invalid duration';
            break;
        }

        // TTL CAP – prevent unrealistic retention values
        $max_ttl = 5 * 365 * 24 * 60 * 60;
        $message_ttl = min((int)$message_ttl_raw, $max_ttl);
        $account_ttl = min((int)$account_ttl_raw, $max_ttl);

        // OWNER CHECK – only the password holder can change settings
        $ownerCheck = $conn->prepare("SELECT password_hash FROM sb_links WHERE public_id = ?");
        $ownerCheck->execute([$public_id]);
        $ownerRow = $ownerCheck->fetch();

        if (!$ownerRow || !password_verify($auth_key, $ownerRow['password_hash'])) {
            record_failed_auth_attempt(getClientIp());
            $response['error'] = 'Invalid password';
            break;
        }

        $messageTtlParam = $message_ttl > 0 ? $message_ttl : null;
        $accountTtlParam = $account_ttl > 0 ? $account_ttl : null;

        try {
            $stmt = $conn->prepare("UPDATE sb_links SET message_ttl_seconds = ?, account_ttl_seconds = ? WHERE public_id = ?");
            $stmt->execute([$messageTtlParam, $accountTtlParam, $public_id]);
            $response['success'] = true;
        } catch (PDOException $e) {
            error_log('api.php: set_auto_delete_settings failed — ' . $e->getMessage());
            $response['error'] = 'Could not save auto-delete settings';
        }
        break;

    // DELETE LINK – owner removes link and messages together
    case 'delete_link':
        $public_id = sanitize_input($_POST['public_id'] ?? '');
        $auth_key  = $_POST['auth_key'] ?? '';

        if (empty($public_id) || !validate_public_id($public_id)) {
            $response['error'] = 'Invalid or missing public_id';
            break;
        }
        if (empty($auth_key)) {
            $response['error'] = 'Auth key required to delete this link';
            break;
        }

        // OWNER CHECK – only the password holder can delete
        $ownerCheck = $conn->prepare("SELECT password_hash FROM sb_links WHERE public_id = ?");
        $ownerCheck->execute([$public_id]);
        $ownerRow = $ownerCheck->fetch();

        if (!$ownerRow || !password_verify($auth_key, $ownerRow['password_hash'])) {
            record_failed_auth_attempt(getClientIp());
            $response['error'] = 'Invalid auth key';
            break;
        }

        try {
            // TRANSACTION – keep the message + link delete atomic
            $conn->beginTransaction();

            // MESSAGE DELETE – don't leave orphan ciphertext behind
            $stmt1 = $conn->prepare("DELETE FROM sb_messages WHERE link_public_id = ?");
            $stmt1->execute([$public_id]);

            $stmt2 = $conn->prepare("DELETE FROM sb_links WHERE public_id = ?");
            $stmt2->execute([$public_id]);

            $conn->commit();
            $response['success'] = true;

            // SESSION WIPE – clear all server-side session data and its cookie
            session_reopen();
            $_SESSION = [];
            session_commit();
            if (ini_get('session.use_cookies')) {
                $cookieParams = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $cookieParams['path'],
                    $cookieParams['domain'],
                    $cookieParams['secure'],
                    $cookieParams['httponly']
                );
            }
            $response['clear_client_storage'] = true;
        } catch (PDOException $e) {
            $conn->rollBack();
            error_log('api.php: delete_link failed — ' . $e->getMessage());
            $response['error'] = 'Could not delete link';
        }
        break;

    // DEFAULT – reject unknown actions instead of guessing
    default:
        $response['error'] = 'Invalid action';
}
echo json_encode($response);

$conn = null;

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================

?>