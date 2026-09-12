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
            : ['get_csrf', 'get_public_key', 'get_messages'];

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
header("X-XSS-Protection: 1; mode=block");
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

// STORAGE – define paths for security files
if (!defined('STORAGE_DIR')) {
    define('STORAGE_DIR', __DIR__ . '/storage');
}
if (!defined('BLACKLIST_FILE')) {
    define('BLACKLIST_FILE', STORAGE_DIR . '/blacklist.json');
}
if (!defined('RATE_LIMIT_DIR')) {
    define('RATE_LIMIT_DIR', STORAGE_DIR . '/rate_limits');
}
if (!defined('BURNED_IPS_FILE')) {
    define('BURNED_IPS_FILE', STORAGE_DIR . '/burned_ips.json');
}

foreach ([STORAGE_DIR, RATE_LIMIT_DIR] as $dir) {
    if (!is_dir($dir)) {
        if (!@mkdir($dir, 0755, true)) {
            error_log("Secret Gate: cannot create directory $dir (permissions?)");
        }
    }
}

// CLIENT IP – resolve real IP behind proxies
if (!function_exists('getClientIp')) {
    function getClientIp(): string {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $h) {
            if (!empty($_SERVER[$h])) {
                $ips = explode(',', $_SERVER[$h]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

// REDIS – lazy connection with fallback
if (!function_exists('get_redis')) {
    function get_redis(): ?Redis {
        static $redis = null;
        static $failed = false;

        if ($failed) {
            return null;
        }

        if ($redis === null) {
            if (!class_exists('Redis')) {
                $failed = true;
                return null;
            }
            try {
                $redis = new Redis();
                $connected = $redis->connect(getenv('REDIS_HOST') ?: '127.0.0.1', (int)(getenv('REDIS_PORT') ?: 6379), 1.0);
                if (!$connected) {
                    throw new Exception('connect() returned false');
                }
                $redis_pass = getenv('REDIS_PASS') ?: '';
                if ($redis_pass !== '') {
                    $redis->auth($redis_pass);
                }
            } catch (Exception $e) {
                error_log('Redis connection failed: ' . $e->getMessage());
                $redis = null;
                $failed = true;
            }
        }

        return $redis;
    }
}

// BLACKLIST – manage temporary IP bans
if (!function_exists('is_ip_blacklisted')) {
    function is_ip_blacklisted(string $ip): bool {
        $redis = get_redis();
        if ($redis) {
            try {
                return (bool)$redis->exists('sb:blacklist:' . $ip);
            } catch (Exception $e) {
                error_log('Redis is_ip_blacklisted failed: ' . $e->getMessage());
            }
        }
        if (!@file_exists(BLACKLIST_FILE)) return false;
        $raw = @file_get_contents(BLACKLIST_FILE);
        if ($raw === false) return false;
        $data = json_decode($raw, true);
        return isset($data[$ip]) && ($data[$ip]['until'] ?? 0) > time();
    }
}

if (!function_exists('add_to_blacklist')) {
    function add_to_blacklist(string $ip, int $duration = 86400, string $reason = ''): void {
        $redis = get_redis();
        if ($redis) {
            try {
                $redis->setex('sb:blacklist:' . $ip, $duration, json_encode([
                    'reason' => $reason,
                    'time' => time(),
                ]));
                return;
            } catch (Exception $e) {
                error_log('Redis add_to_blacklist failed: ' . $e->getMessage());
            }
        }
        $raw = @file_exists(BLACKLIST_FILE) ? @file_get_contents(BLACKLIST_FILE) : '';
        $data = $raw ? (json_decode($raw, true) ?: []) : [];
        $data[$ip] = ['until' => time() + $duration, 'reason' => $reason, 'time' => time()];
        @file_put_contents(BLACKLIST_FILE, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    }
}

if (!function_exists('remove_from_blacklist')) {
    function remove_from_blacklist(string $ip): void {
        $redis = get_redis();
        if ($redis) {
            try {
                $redis->del('sb:blacklist:' . $ip);
            } catch (Exception $e) {
                error_log('Redis remove_from_blacklist failed: ' . $e->getMessage());
            }
        }
        if (!@file_exists(BLACKLIST_FILE)) return;
        $raw = @file_get_contents(BLACKLIST_FILE);
        if ($raw === false) return;
        $data = json_decode($raw, true);
        if (isset($data[$ip])) {
            unset($data[$ip]);
            @file_put_contents(BLACKLIST_FILE, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
        }
    }
}

// BURN – permanently block persistent attackers
if (!function_exists('burn_ip')) {
    function burn_ip(string $ip, string $reason = 'Persistent attacker'): void {
        $redis = get_redis();
        if ($redis) {
            try {
                if ($redis->hExists('sb:burned_ips', $ip)) return;
                $redis->hSet('sb:burned_ips', $ip, json_encode([
                    'burned_at' => time(),
                    'reason' => $reason,
                ]));
                return;
            } catch (Exception $e) {
                error_log('Redis burn_ip failed: ' . $e->getMessage());
            }
        }
        $raw = @file_exists(BURNED_IPS_FILE) ? @file_get_contents(BURNED_IPS_FILE) : '';
        $burned = $raw ? (json_decode($raw, true) ?: []) : [];
        if (isset($burned[$ip])) return;
        $burned[$ip] = ['burned_at' => time(), 'reason' => $reason];
        @file_put_contents(BURNED_IPS_FILE, json_encode($burned, JSON_PRETTY_PRINT), LOCK_EX);
    }
}

if (!function_exists('is_ip_burned')) {
    function is_ip_burned(string $ip): bool {
        $redis = get_redis();
        if ($redis) {
            try {
                return (bool)$redis->hExists('sb:burned_ips', $ip);
            } catch (Exception $e) {
                error_log('Redis is_ip_burned failed: ' . $e->getMessage());
            }
        }
        if (!@file_exists(BURNED_IPS_FILE)) return false;
        $raw = @file_get_contents(BURNED_IPS_FILE);
        if ($raw === false) return false;
        $data = json_decode($raw, true);
        return isset($data[$ip]);
    }
}

// RATE LIMIT – throttle requests per IP
if (!function_exists('rate_limit_check')) {
    function rate_limit_check(string $ip, int $limit = 30, int $window = 30): bool {
        $redis = get_redis();
        if ($redis) {
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
            }
        }

        $file = RATE_LIMIT_DIR . '/' . md5($ip) . '.json';
        $now = time();
        $raw = @file_exists($file) ? @file_get_contents($file) : '';
        $data = $raw ? (json_decode($raw, true) ?: []) : [];
        $data['requests'] = array_filter(
            $data['requests'] ?? [],
            fn($t) => $t > $now - $window
        );
        if (count($data['requests']) >= $limit) {
            return false;
        }
        $data['requests'][] = $now;
        if (@file_put_contents($file, json_encode($data), LOCK_EX) === false) {
            error_log("Secret Gate: cannot write rate-limit file $file (permissions?) — allowing request through");
        }
        return true;
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

// INIT FLAG – mark security as loaded
if (!defined('SECURITY_INIT')) {
    define('SECURITY_INIT', true);
}

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================

?>