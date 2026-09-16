<?php


/**
 * SECRET GATE - ZERO-KNOWLEDGE EPHEMERAL MESSAGING ENGINE
 *
 * @package     Secret-Gate
 * @version     1.5.0-Release
 * 
 * @author      Sasiru Mindaka <info@secretgate.site>
 * @copyright   2026 Sasiru Mindaka
 * 
 * @license     AGPL-3.0-or-later <https://www.gnu.org/licenses/agpl-3.0.html>
 * @link        https://github.com/sasiru-mindaka/Secret-Gate
 * @see         https://secretgate.site
 */


// INCLUSION GUARD – prevent duplicate setup on multiple includes. Placed
// first so a second require_once (or a require, should one ever slip in)
// can't re-run loadEnv() and the constant checks below.
if (defined('CONFIG_LOADED')) {
    return;
}
define('CONFIG_LOADED', true);

// OUTPUT BUFFERING – prevent "headers already sent" errors
if (!defined('SB_OUTPUT_BUFFER_STARTED')) {
    define('SB_OUTPUT_BUFFER_STARTED', true);
    ob_start();
}

// ENV LOADER – keep credentials out of source control
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        die("The .env file could not be found!");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '#') === 0) continue;

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            // Strip a trailing unquoted "# comment" so "KEY=value # note" doesn't
            // become part of the value. Only do this when the value isn't quoted,
            // so a literal '#' inside a quoted value is preserved.
            if ($value !== '' && $value[0] !== '"' && $value[0] !== "'") {
                $value = preg_replace('/\s+#.*$/', '', $value);
            }
            $value = trim(trim($value), '"\'');

            if ($name !== '' && !array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

loadEnv(__DIR__ . '/../.env');  // Add your env path here

// SITE-WIDE CONSTANTS – single source of truth so pages/footers/cards don't
// hardcode the domain, contact email, or repo URL in a dozen places.
if (!defined('SITE_URL')) {
    define('SITE_URL', rtrim(getenv('SITE_URL') ?: 'https://secretgate.site', '/'));
}
if (!defined('SITE_EMAIL')) {
    define('SITE_EMAIL', getenv('SITE_EMAIL') ?: 'info@secretgate.site');
}
if (!defined('GITHUB_REPO')) {
    define('GITHUB_REPO', 'https://github.com/sasiru-mindaka/Secret-Gate'); // confirm this path
}

// ACCESS CONTROL – only allow inclusion from whitelisted scripts
$allowed_scripts = [
    'index.php',
    'api.php',
    'social-card.php',
    'feedback.php',
    'feedback-api.php',
    '404.php',
    '403.php',
    'privacy.php',
    'terms.php',
    'donate.php',
];

$current_script = basename($_SERVER['SCRIPT_NAME'] ?? '');

if (!in_array($current_script, $allowed_scripts, true)) {
    http_response_code(403);
    include __DIR__ . '/403.php';
    exit;
}

// READ-ONLY GET WHITELIST – define which actions are safe to expose via GET
if (!defined('API_READONLY_GET_ACTIONS')) {
    define('API_READONLY_GET_ACTIONS', [
        'get_csrf',
        'get_public_key',
        'get_messages',
        'get_auto_delete_settings',
        'get_auth_salt',
    ]);
}

// API METHOD GATE – prevent state changes via GET
if ($current_script === 'api.php') {
    $req_method = $_SERVER['REQUEST_METHOD'] ?? '';
    $req_action = $_GET['action'] ?? '';

    $is_allowed_readonly_get =
        $req_method === 'GET' &&
        in_array($req_action, API_READONLY_GET_ACTIONS, true);

    if (
        $req_method !== 'POST' &&
        $req_method !== 'OPTIONS' &&
        !$is_allowed_readonly_get
    ) {
        http_response_code(403);
        include __DIR__ . '/403.php';
        exit;
    }
}

// FEEDBACK API GATE – only allow internal AJAX calls
if ($current_script === 'feedback-api.php') {
    $req_method = $_SERVER['REQUEST_METHOD'] ?? '';
    $ajax_header = strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    // XHR ONLY – block direct browser navigation
    if ($ajax_header !== 'xmlhttprequest') {
        http_response_code(403);
        include __DIR__ . '/403.php';
        exit;
    }

    // METHOD BINDING – restrict each action to its intended HTTP method
    $req_action = $_GET['action'] ?? $_POST['action'] ?? '';

    if (
        ($req_action === 'get_csrf' && $req_method !== 'GET') ||
        ($req_action === 'send_feedback' && $req_method !== 'POST') ||
        !in_array($req_action, ['get_csrf', 'send_feedback'], true)
    ) {
        http_response_code(403);
        include __DIR__ . '/403.php';
        exit;
    }

    // SAME-ORIGIN CHECK – reject requests from other hosts
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $normalize_host = static function ($value) {
        $value = strtolower((string)$value);
        return preg_replace('/^www\./', '', $value);
    };

    $hostOnlyFromUrl = static function ($url) {
        if ($url === '') return '';
        return (string)(parse_url($url, PHP_URL_HOST) ?? '');
    };

    if ($origin !== '') {
        $originHost = $hostOnlyFromUrl($origin);
        if ($originHost === '' || $normalize_host($originHost) !== $normalize_host($host)) {
            http_response_code(403);
            include __DIR__ . '/403.php';
            exit;
        }
    }

    if ($referer !== '') {
        $refererHost = $hostOnlyFromUrl($referer);
        if ($refererHost === '' || $normalize_host($refererHost) !== $normalize_host($host)) {
            http_response_code(403);
            include __DIR__ . '/403.php';
            exit;
        }
    }
}

// ERROR REPORTING – hide errors in production, log everything
$app_env = getenv('APP_ENV') ?: 'production';
ini_set('display_errors', $app_env === 'development' ? 1 : 0);
ini_set('display_startup_errors', $app_env === 'development' ? 1 : 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// SESSION SECURITY – harden cookie flags against theft
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');

$is_https =
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    // Require HTTPS in production; allow plain-HTTP localhost during development
    // so the session cookie isn't silently dropped by the browser there.
    'secure' => $is_https || $app_env === 'production',
    'httponly' => true,
    'samesite' => 'Strict',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start(['read_and_close' => true]);
}

// SESSION ID SYNC – prevent orphaned CSRF tokens from multiple session IDs
$_COOKIE[session_name()] = session_id();

// SESSION WRITE HELPERS – allow selective write access when needed
if (!function_exists('session_reopen')) {
    function session_reopen(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $pending = $_SESSION ?? [];
            session_start();
            $_SESSION = $pending;
        }
    }
}

if (!function_exists('session_commit')) {
    function session_commit(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }
}

// SHUTDOWN SAFETY NET – ensure session is written and output flushed
register_shutdown_function(function () {
    // Only flush a session that a handler actually reopened with
    // session_reopen() (PHP_SESSION_ACTIVE). If nothing reopened it, the
    // session is PHP_SESSION_NONE and must stay that way — starting one here
    // would create/write an empty session file for every plain GET request.
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    if (ob_get_level() > 0) {
        ob_end_flush();
    }
});

// SECURITY LAYER – load shared protection functions
require_once __DIR__ . '/security.php';

// DATABASE CREDENTIALS – pull from environment variables
$db_host = getenv('DB_HOST') ?: '';
$db_port = getenv('DB_PORT') ?: '';
$db_user = getenv('DB_USER') ?: '';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: '';

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================

?>