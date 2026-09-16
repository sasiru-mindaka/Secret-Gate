<?php


/**
 * SECRET GATE - ZERO-KNOWLEDGE EPHEMERAL ENGINE
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
 * 
 */


require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// JSON RESPONSE – standardize API output
function feedback_json_out(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// HOST NORMALIZATION – compare domains without www
function feedback_normalize_host(?string $host): string {
    $host = strtolower(trim($host ?? ''));
    return preg_replace('/^www\./', '', $host) ?? '';
}

// HTTP POST – avoid fatal error if curl missing
function feedback_http_post(string $url, array $fields, int $timeoutSeconds = 8): array {
    $body = http_build_query($fields);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch !== false) {
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeoutSeconds,
            ]);
            $raw = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            if ($raw !== false) {
                return ['ok' => true, 'body' => $raw];
            }
            error_log("feedback-api: curl POST to $url failed — $err");
            return ['ok' => false, 'body' => null];
        }
    }

    // Fallback path — no curl extension available.
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $body,
            'timeout' => $timeoutSeconds,
            'ignore_errors' => true,
        ],
    ]);
    $raw = @file_get_contents($url, false, $context);
    if ($raw !== false) {
        return ['ok' => true, 'body' => $raw];
    }
    error_log("feedback-api: file_get_contents POST to $url failed (curl extension unavailable)");
    return ['ok' => false, 'body' => null];
}

// ACCESS RESTRICTION – block direct browser access
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
$host = $_SERVER['HTTP_HOST'] ?? '';

$originHost  = isset($_SERVER['HTTP_ORIGIN'])
    ? (parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST) ?? '')
    : '';

$refererHost = isset($_SERVER['HTTP_REFERER'])
    ? (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ?? '')
    : '';

$ajaxHeader = strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));

$hostNormalized = feedback_normalize_host($host);
$originOk  = $originHost !== '' && feedback_normalize_host($originHost) === $hostNormalized;
$refererOk = $refererHost !== '' && feedback_normalize_host($refererHost) === $hostNormalized;

$originWrong  = $originHost !== '' && !$originOk;
$refererWrong = $refererHost !== '' && !$refererOk;

$isAjaxRequest = $ajaxHeader === 'xmlhttprequest';

$requestedAction = $_GET['action'] ?? $_POST['action'] ?? '';

$methodAllowed = in_array($method, ['GET', 'POST'], true);
$actionAllowed = in_array($requestedAction, ['get_csrf', 'send_feedback'], true);

if (
    !$methodAllowed ||
    !$actionAllowed ||
    !$isAjaxRequest ||
    $originWrong ||
    $refererWrong
) {
    http_response_code(403);
    $forbidden = __DIR__ . '/403.php';

    if (is_file($forbidden)) {
        include $forbidden;
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Forbidden.'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    exit;
}

// CORS – allow same-origin only
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && $originOk) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

header('Access-Control-Allow-Credentials: true');
header('Vary: Origin');

if ($method === 'OPTIONS') {
    http_response_code(403);
    exit;
}

// ACTION VALIDATION – enforce correct HTTP method
$action = $requestedAction;

if ($action === 'send_feedback' && $method !== 'POST') {
    feedback_json_out(['success' => false, 'error' => 'POST required.'], 200);
}

if ($action === 'get_csrf' && $method !== 'GET') {
    feedback_json_out(['success' => false, 'error' => 'GET required.'], 200);
}

// CSRF TOKEN – generate and store for form security
if ($action === 'get_csrf') {
    session_reopen();

    $token = bin2hex(random_bytes(32));
    $_SESSION['feedback_csrf'] = $token;
    $_SESSION['feedback_csrf_time'] = time();

    session_commit();

    feedback_json_out([
        'success' => true,
        'csrf_token' => $token
    ]);
}

// CSRF CHECK – prevent cross-site request forgery
session_reopen();
$sessionToken = $_SESSION['feedback_csrf'] ?? '';
$sessionTokenTime = $_SESSION['feedback_csrf_time'] ?? 0;
session_commit();

$submittedToken = $_POST['csrf_token'] ?? '';
$tokenExpired = ((int)$sessionTokenTime === 0)
    || (time() - (int)$sessionTokenTime) > 1800;

if (
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !is_string($submittedToken) ||
    $submittedToken === '' ||
    $tokenExpired ||
    !hash_equals($sessionToken, $submittedToken)
) {
    feedback_json_out([
        'success' => false,
        'error' => 'Session expired. Please reload the page and try again.'
    ], 200);
}

// HONEYPOT – silently reject bots
if (!empty($_POST['website'] ?? '')) {
    feedback_json_out(['success' => true]);
}

// RATE LIMITING – prevent spam
$clientIp = getClientIp();
if (!rate_limit_check('feedback_' . $clientIp, 5, 600)) {
    feedback_json_out([
        'success' => false,
        'error' => 'Too many submissions. Please wait a while before sending more feedback.'
    ], 200);
}

// TURNSTILE – verify human via Cloudflare
$turnstileToken = $_POST['cf-turnstile-response'] ?? '';
$turnstileSecret = getenv('CF_TURNSTILE_SECRET_KEY') ?: '';

if ($turnstileToken === '' || $turnstileSecret === '') {
    if ($turnstileSecret === '') {
        error_log('feedback-api: CF_TURNSTILE_SECRET_KEY not configured in .env');
    }
    feedback_json_out([
        'success' => false,
        'error' => 'Verification failed. Please try again.'
    ], 200);
}

$verifyResult = feedback_http_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
    'secret' => $turnstileSecret,
    'response' => $turnstileToken,
    'remoteip' => $clientIp,
], 8);

if (!$verifyResult['ok']) {
    feedback_json_out([
        'success' => false,
        'error' => 'Verification service unavailable. Please try again.'
    ], 200);
}
$verifyRaw = $verifyResult['body'];

$verifyData = json_decode($verifyRaw, true);
if (!is_array($verifyData) || empty($verifyData['success'])) {
    error_log('feedback-api: Turnstile verification failed — ' . $verifyRaw);
    feedback_json_out([
        'success' => false,
        'error' => 'Verification failed. Please try again.'
    ], 200);
}

// MESSAGE VALIDATION – clean and limit input
$message = trim((string)($_POST['message'] ?? ''));

$message = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $message) ?? '';

if ($message === '') {
    feedback_json_out([
        'success' => false,
        'error' => 'Please write a message before sending.'
    ], 200);
}

if (mb_strlen($message) > 4000) {
    feedback_json_out([
        'success' => false,
        'error' => 'Message is too long (max 4000 characters).'
    ], 200);
}

// TELEGRAM RELAY – forward feedback to bot
$botToken = getenv('TELEGRAM_BOT_TOKEN') ?: '';
$chatId = getenv('TELEGRAM_CHAT_ID') ?: '';

if ($botToken === '' || $chatId === '') {
    error_log('feedback-api: TELEGRAM_BOT_TOKEN / TELEGRAM_CHAT_ID not configured in .env');
    feedback_json_out([
        'success' => false,
        'error' => 'Feedback is temporarily unavailable. Please try again later.'
    ], 200);
}

$telegramText = "New Feedback — Secret Gate\n\n" . $message;
$telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

$tgResult = feedback_http_post($telegramUrl, [
    'chat_id' => $chatId,
    'text' => $telegramText,
    'disable_web_page_preview' => true,
], 8);

if (!$tgResult['ok']) {
    feedback_json_out([
        'success' => false,
        'error' => 'Could not deliver feedback right now. Please try again later.'
    ], 200);
}
$tgRaw = $tgResult['body'];

$tgData = json_decode($tgRaw, true);
if (!is_array($tgData) || empty($tgData['ok'])) {
    error_log('feedback-api: Telegram API rejected the message — ' . $tgRaw);
    feedback_json_out([
        'success' => false,
        'error' => 'Could not deliver feedback right now. Please try again later.'
    ], 200);
}

// TOKEN BURN – prevent replay attacks
session_reopen();
unset($_SESSION['feedback_csrf'], $_SESSION['feedback_csrf_time']);
session_commit();

feedback_json_out(['success' => true]);

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================