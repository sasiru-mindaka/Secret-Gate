<?php


/**
 * SECRET GATE - ZERO-KNOWLEDGE EPHEMERAL MESSAGING ENGINE
 *
 * @package     Secret-Gate
 * @version     2.0.0-Release
 * 
 * @author      Sasiru Mindaka <info@secretgate.site>
 * @copyright   2026 Sasiru Mindaka
 * 
 * @license     AGPL-3.0-or-later <https://www.gnu.org/licenses/agpl-3.0.html>
 * @link        https://github.com/sasiru-mindaka/Secret-Gate
 * @see         https://secretgate.site
 */


// SESSION – avoid locking for read-only access
if (session_status() === PHP_SESSION_NONE) {
    session_start(['read_and_close' => true]);
}

// ACCESS CONTROL – prevent direct inclusion from unapproved scripts
if (php_sapi_name() !== 'cli') {
    $allowed_scripts = [
        'config.php',
        'index.php',
        'api.php',
        'social-card.php',
        'feedback.php',
        'feedback-api.php',
        'privacy.php',
        '404.php',
        '403.php',
        'terms.php',
        'donate.php',
    ];

    $current_script = basename($_SERVER['SCRIPT_NAME'] ?? '');

    if (!in_array($current_script, $allowed_scripts, true)) {
        http_response_code(403);
        include __DIR__ . '/403.php';
        exit;
    }

    // API PROTECTION – limit to allowed methods and actions
    if ($current_script === 'api.php') {
        $req_method = $_SERVER['REQUEST_METHOD'] ?? '';
        $req_action = $_GET['action'] ?? '';
        $readonly_actions = defined('API_READONLY_GET_ACTIONS')
            ? API_READONLY_GET_ACTIONS
            : ['get_csrf', 'get_public_key', 'get_messages', 'get_auto_delete_settings', 'get_auth_salt'];

        $is_allowed_readonly_get =
            $req_method === 'GET' &&
            in_array($req_action, $readonly_actions, true);

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

    // FEEDBACK API – require AJAX and allowed actions
    if ($current_script === 'feedback-api.php') {
        $req_method = $_SERVER['REQUEST_METHOD'] ?? '';
        $req_action = $_GET['action'] ?? $_POST['action'] ?? '';
        $xRequestedWith = strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));

        $isAjax = $xRequestedWith === 'xmlhttprequest';
        $isAllowedAction = in_array($req_action, ['get_csrf', 'send_feedback'], true);

        if (!$isAjax || !$isAllowedAction) {
            http_response_code(403);
            include __DIR__ . '/403.php';
            exit;
        }

        if ($req_action === 'get_csrf' && $req_method !== 'GET') {
            http_response_code(405);
            header('Allow: GET');
            exit('Method Not Allowed');
        }

        if ($req_action === 'send_feedback' && $req_method !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Method Not Allowed');
        }
    }
}

// SECURITY HEADERS – harden browser protections
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 0");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Cross-Origin-Resource-Policy: same-site");
header("Cross-Origin-Opener-Policy: same-origin");
header("X-DNS-Prefetch-Control: off");
header("Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), accelerometer=(), gyroscope=(), magnetometer=(), autoplay=(), encrypted-media=(), picture-in-picture=(), clipboard-read=(), clipboard-write=(self), fullscreen=(), screen-wake-lock=(), sync-xhr=(), xr-spatial-tracking=()");
header_remove("X-Powered-By");

// CSP NONCE – generate unique token for inline scripts
if (!defined('CSP_NONCE')) {
    $csp_nonce = base64_encode(random_bytes(16));
    define('CSP_NONCE', $csp_nonce);
} else {
    $csp_nonce = CSP_NONCE;
}

// CSP – restrict resource loading to trusted sources
$csp = "default-src 'self'; " .
       "script-src 'self' 'nonce-$csp_nonce' https://challenges.cloudflare.com https://cdnjs.cloudflare.com; " .
       "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
       "font-src 'self' https://fonts.gstatic.com; " .
       "connect-src 'self' https://challenges.cloudflare.com; " .
       "frame-src 'self' https://challenges.cloudflare.com; " .
       "form-action 'self'; " .
       "frame-ancestors 'self';";
header("Content-Security-Policy: " . $csp);

// CORS – control cross-origin access
$allowedOrigins = [];
$envOrigins = getenv('CORS_ALLOWED_ORIGINS');
$corsHostFallback = null;

if ($envOrigins) {
    $allowedOrigins = array_values(array_filter(array_map('trim', explode(',', $envOrigins))));
} else {
    $corsHostFallback = strtolower($_SERVER['HTTP_HOST'] ?? '');
}

if (!function_exists('cors_origin_is_allowed')) {
    function cors_origin_is_allowed(string $origin, array $allowedOrigins, ?string $corsHostFallback): bool {
        if ($corsHostFallback !== null) {
            $originHost = strtolower((string)(parse_url($origin, PHP_URL_HOST) ?? ''));
            return $originHost !== '' && $originHost === $corsHostFallback;
        }
        return in_array($origin, $allowedOrigins, true);
    }
}

if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    if (cors_origin_is_allowed($origin, $allowedOrigins, $corsHostFallback)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
        header("Access-Control-Max-Age: 86400");
    } else {
        $current_script = basename($_SERVER['SCRIPT_NAME'] ?? '');
        if (in_array($current_script, ['api.php', 'feedback-api.php'], true)) {
            http_response_code(403);
            include __DIR__ . '/403.php';
            exit;
        }
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    if (isset($_SERVER['HTTP_ORIGIN']) && !cors_origin_is_allowed($_SERVER['HTTP_ORIGIN'], $allowedOrigins, $corsHostFallback)) {
        http_response_code(403);
        exit;
    }
    http_response_code(200);
    exit;
}

// =====================================================================
// REDIS CONNECTION — single attempt per request
// =====================================================================
if (!function_exists('get_redis')) {
    function get_redis(): ?Redis {
        static $redis = null;
        static $tried = false;

        if ($tried) return $redis;
        $tried = true;

        if (!class_exists('Redis')) {
            error_log('Secret Gate: PHP redis extension not loaded.');
            return null;
        }

        try {
            $r = new Redis();
            $connected = $r->connect(
                getenv('REDIS_HOST') ?: '127.0.0.1',
                (int)(getenv('REDIS_PORT') ?: 6379),
                0.5
            );

            if (!$connected) {
                error_log('Secret Gate: Redis connect() returned false.');
                return null;
            }

            $pass = getenv('REDIS_PASS') ?: '';
            if ($pass !== '') {
                $r->auth($pass);
            }

            $pong = $r->ping();
            if ($pong !== true && $pong !== '+PONG') {
                error_log('Secret Gate: Redis PING failed after connect.');
                return null;
            }

            $redis = $r;
        } catch (Exception $e) {
            error_log('Secret Gate: Redis connection failed — ' . $e->getMessage());
            return null;
        }

        return $redis;
    }
}

// =====================================================================
// HARD FAIL-CLOSED — Redis is required, no fallback, no disk writes
// =====================================================================
$redis_health = get_redis();

if ($redis_health === null) {
    // Log once — not per request. Use a rate-limited file flag.
    $flag = sys_get_temp_dir() . '/sg_redis_down.flag';
    $should_log = !file_exists($flag) || (time() - filemtime($flag)) > 300;
    if ($should_log) {
        @touch($flag);
        error_log('Secret Gate: Redis unavailable — serving 503 maintenance page.');
    }

    http_response_code(503);
    header('Retry-After: 30');
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header_remove('Content-Security-Policy');
    ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Secret Gate — Maintenance</title>
    <link rel="icon" type="image/png" href="./images/secret_gate_logo.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #000; color: #fff; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 24px;
        }
        .card {
            max-width: 440px; text-align: center;
            background: #111317; border: 1px solid rgba(76,80,85,0.7);
            border-radius: 28px; padding: 40px 32px;
            box-shadow: 0 18px 50px rgba(0,0,0,0.45);
        }
        .badge {
            display: inline-block;
            font-size: 0.7rem; letter-spacing: 2.5px; color: #FF8F00;
            text-transform: uppercase; margin-bottom: 18px;
        }
        h1 {
            font-size: 1.6rem; font-weight: 600; margin-bottom: 12px;
            background: linear-gradient(to bottom, #fff 0%, #9aa1ab 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        p {
            color: rgba(255,255,255,0.68); font-size: 0.9rem;
            line-height: 1.6; margin-bottom: 20px;
        }
        .timer {
            font-size: 0.78rem; color: #47A5FF;
            padding: 10px 18px; border-radius: 999px;
            background: rgba(71,165,255,0.08);
            border: 1px solid rgba(71,165,255,0.2);
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">● Service Temporarily Unavailable</div>
        <h1>We'll Be Right Back</h1>
        <p>Secret Gate is briefly unavailable while we restore a core service. Your messages remain encrypted and safe. Please refresh in a moment.</p>
        <div class="timer">Auto-retry: <span id="t">30</span>s</div>
    </div>
    <script>
        var s = 30, el = document.getElementById('t');
        setInterval(function () {
            s--;
            if (s <= 0) { location.reload(); return; }
            el.textContent = s;
        }, 1000);
    </script>
</body>
</html><?php
    exit;
}

// Redis is alive — clear the "down" flag so next failure logs again
$flag = sys_get_temp_dir() . '/sg_redis_down.flag';
if (file_exists($flag)) @unlink($flag);

// =====================================================================
// CLIENT IP — trusted-proxy aware (Cloudflare)
// =====================================================================
if (!function_exists('getClientIp')) {
    function getClientIp(): string {
        $remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $cf_ranges = [
                '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22',
                '103.31.4.0/22', '141.101.64.0/18', '108.162.192.0/18',
                '190.93.240.0/20', '188.114.96.0/20', '197.234.240.0/22',
                '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
                '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
            ];
            $ip_long = ip2long($remote);
            if ($ip_long !== false) {
                foreach ($cf_ranges as $cidr) {
                    list($subnet, $mask) = explode('/', $cidr);
                    $subnet_long = ip2long($subnet);
                    $mask_long = -1 << (32 - (int)$mask);
                    if (($ip_long & $mask_long) === ($subnet_long & $mask_long)) {
                        $cf_ip = filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP);
                        if ($cf_ip) return $cf_ip;
                        break;
                    }
                }
            }
        }

        return $remote;
    }
}

// =====================================================================
// BLACKLIST / BURN / RATE LIMIT — Redis only, fail-closed
// =====================================================================

if (!function_exists('is_ip_blacklisted')) {
    function is_ip_blacklisted(string $ip): bool {
        $redis = get_redis();
        if (!$redis) return true; // fail-closed
        try {
            return (bool)$redis->exists('sb:blacklist:' . $ip);
        } catch (Exception $e) {
            error_log('Redis is_ip_blacklisted failed: ' . $e->getMessage());
            return true;
        }
    }
}

if (!function_exists('add_to_blacklist')) {
    function add_to_blacklist(string $ip, int $duration = 86400, string $reason = ''): void {
        $redis = get_redis();
        if (!$redis) return;
        try {
            $redis->setex('sb:blacklist:' . $ip, $duration, json_encode([
                'reason' => $reason,
                'time' => time(),
            ]));
        } catch (Exception $e) {
            error_log('Redis add_to_blacklist failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('remove_from_blacklist')) {
    function remove_from_blacklist(string $ip): void {
        $redis = get_redis();
        if (!$redis) return;
        try {
            $redis->del('sb:blacklist:' . $ip);
        } catch (Exception $e) {
            error_log('Redis remove_from_blacklist failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('burn_ip')) {
    function burn_ip(string $ip, string $reason = 'Persistent attacker'): void {
        $redis = get_redis();
        if (!$redis) return;
        try {
            if ($redis->hExists('sb:burned_ips', $ip)) return;
            $redis->hSet('sb:burned_ips', $ip, json_encode([
                'burned_at' => time(),
                'reason' => $reason,
            ]));
        } catch (Exception $e) {
            error_log('Redis burn_ip failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('is_ip_burned')) {
    function is_ip_burned(string $ip): bool {
        $redis = get_redis();
        if (!$redis) return true; // fail-closed
        try {
            return (bool)$redis->hExists('sb:burned_ips', $ip);
        } catch (Exception $e) {
            error_log('Redis is_ip_burned failed: ' . $e->getMessage());
            return true;
        }
    }
}

if (!function_exists('record_failed_auth_attempt')) {
    function record_failed_auth_attempt(string $ip, int $limit = 8, int $window = 300, int $banDuration = 1800): void {
        $redis = get_redis();
        if (!$redis) return;
        try {
            $key = 'sb:failauth:' . $ip;
            $count = $redis->incr($key);
            if ($count === 1) {
                $redis->expire($key, $window);
            }
            if ($count >= $limit) {
                add_to_blacklist($ip, $banDuration, 'Too many failed password attempts');
            }
        } catch (Exception $e) {
            error_log('Redis record_failed_auth_attempt failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('rate_limit_check')) {
    function rate_limit_check(string $ip, int $limit = 30, int $window = 30): bool {
        $redis = get_redis();
        if (!$redis) return false; // fail-closed
        try {
            $key = 'sb:rl:' . md5($ip . ':' . $limit . ':' . $window);
            $now = microtime(true);
            $redis->zRemRangeByScore($key, 0, $now - $window);
            $count = $redis->zCard($key);
            if ($count >= $limit) {
                return false;
            }
            $redis->zAdd($key, $now, $now . ':' . bin2hex(random_bytes(4)));
            $redis->expire($key, $window + 1);
            return true;
        } catch (Exception $e) {
            error_log('Redis rate_limit_check failed: ' . $e->getMessage());
            return false;
        }
    }
}

// ENFORCE – apply security checks on every request
if (!function_exists('enforce_security')) {
    function enforce_security(): void {
        if (getenv('APP_ENV') === 'development') {
            return;
        }

        $ip = getClientIp();

        if (is_ip_blacklisted($ip) || is_ip_burned($ip)) {
            http_response_code(403);
            exit('Access denied.');
        }

        if (!rate_limit_check($ip, 120, 60)) {
            http_response_code(429);
            exit('Too many requests. Please slow down.');
        }
    }
}

enforce_security();

// CACHE CONTROL – prevent caching of sensitive pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

if (!defined('SECURITY_INIT')) {
    define('SECURITY_INIT', true);
}

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================