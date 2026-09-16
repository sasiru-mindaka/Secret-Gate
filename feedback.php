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
 * 
 */


require_once __DIR__ . '/config.php';

// ACCESS RESTRICTION – allow normal page loads, but reject foreign-site requests
$reqMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');

// Verify Origin/Referer when the browser sends one.
function feedback_normalize_host($host) {
    $host = strtolower(trim((string)$host));
    return preg_replace('/^www\./', '', $host);
}

$serverHost = feedback_normalize_host($_SERVER['HTTP_HOST'] ?? '');

$originHost = '';
if (!empty($_SERVER['HTTP_ORIGIN'])) {
    $originHost = feedback_normalize_host(
        parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST) ?? ''
    );
}

$refererHost = '';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $refererHost = feedback_normalize_host(
        parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ?? ''
    );
}

$originOk  = $originHost !== '' && hash_equals($serverHost, $originHost);
$refererOk = $refererHost !== '' && hash_equals($serverHost, $refererHost);

$hasForeignOrigin = ($originHost !== '' && !$originOk);
$hasForeignReferer = ($refererHost !== '' && !$refererOk);

if (
    $reqMethod !== 'GET' ||
    $hasForeignOrigin ||
    $hasForeignReferer
) {
    http_response_code(403);
    include __DIR__ . '/403.php';
    exit;
}

define('LOADING_ACCESS_ALLOWED', true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Secret Gate</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>

    <link rel="icon" type="image/png" href="./images/secret_gate_logo.png">

    <style nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        :root {
            --bg: #000000;
            --surface: #111317;
            --surface-2: #171a1f;
            --surface-3: #1d2127;
            --border: rgba(76, 80, 85, 0.7);
            --border-soft: rgba(76, 80, 85, 0.35);
            --text: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.68);
            --accent: #47A5FF;
            --accent-soft: #2e86d6;
            --accent-glow: rgba(71, 165, 255, 0.25);
            --amber: #FF8F00;
            --danger: #ff4b6e;
            --success: #9ece6a;
            --shadow: 0 18px 50px rgba(0, 0, 0, 0.45);
            --shadow-hover: 0 28px 70px rgba(0, 0, 0, 0.58);
            --radius-lg: 28px;
            --radius-md: 22px;
            --radius-sm: 16px;

            --font-display: 'Space Grotesk', sans-serif;
            --font-body: 'Space Grotesk', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        html { font-size: 16px; height: 100%; height: 100dvh; overflow: hidden; }

        body {
            height: 100%; height: 100dvh;
            background: var(--bg); color: var(--text); font-family: var(--font-body);
            overflow: hidden; overscroll-behavior: none;
            display: flex; flex-direction: column; position: relative;
        }

        body::before {
            content: ''; position: fixed; inset: 0; z-index: -2; pointer-events: none;
            background:
                radial-gradient(circle at 18% 18%, rgba(71, 165, 255, 0.08), transparent 38%),
                radial-gradient(circle at 82% 12%, rgba(255, 143, 0, 0.05), transparent 36%),
                radial-gradient(circle at 50% 90%, rgba(76, 80, 85, 0.1), transparent 42%);
            background-size: 200% 200%;
            animation: ambientDrift 22s ease-in-out infinite;
        }
        @keyframes ambientDrift {
            0%, 100% { background-position: 0% 0%, 100% 0%, 50% 100%; }
            50% { background-position: 20% 30%, 80% 20%, 40% 80%; }
        }

        #main-wrapper {
            flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center;
            width: 100%; padding: clamp(16px, 4vw, 24px); position: relative; z-index: 1;
            min-height: 0; overflow-y: auto; overflow-x: hidden; -webkit-overflow-scrolling: touch;
            scrollbar-width: none; -ms-overflow-style: none;
        }
        #main-wrapper::-webkit-scrollbar { display: none; }

        .login-blob { position: absolute; border-radius: 50%; filter: blur(72px); opacity: 0.25; pointer-events: none; z-index: 0; }
        .login-blob.b1 { width: 340px; height: 340px; background: radial-gradient(circle, rgba(71,165,255,0.4), transparent 68%); top: -50px; right: -80px; }
        .login-blob.b2 { width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,143,0,0.22), transparent 68%); bottom: -50px; left: -80px; }
        @media (max-width: 480px) { .login-blob { display: none; } }

        .app-container { position: relative; width: 100%; max-width: 500px; z-index: 1; animation: fadeInUp 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) both 0.1s; }
        @keyframes fadeInUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }

        .glass-card {
            position: relative; background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: clamp(28px, 5vw, 44px) clamp(24px, 6vw, 40px);
            box-shadow: var(--shadow), inset 0 1px 0 rgba(255,255,255,0.04);
            transition: border-color 0.4s ease, box-shadow 0.4s ease; text-align: center;
        }
        .glass-card:hover { border-color: rgba(71, 165, 255, 0.3); box-shadow: var(--shadow-hover), inset 0 1px 0 rgba(255,255,255,0.05); }

        .view-label {
            display: inline-flex; align-items: center; gap: 8px;
            font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 2.5px;
            color: var(--amber); text-transform: uppercase; margin-bottom: 14px;
        }
        .view-label::before, .view-label::after { content: ''; width: 14px; height: 1px; background: var(--amber); opacity: 0.6; }

        .view-title {
            font-family: var(--font-display); font-size: clamp(1.6rem, 4.2vw, 2rem); font-weight: 600;
            letter-spacing: -0.02em; margin-bottom: 8px;
            background: linear-gradient(to bottom, #ffffff 0%, #9aa1ab 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .view-desc { font-family: var(--font-mono); font-weight: 300; font-size: 0.84rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 28px; }

        textarea, input[type="text"] {
            width: 100%; max-width: 100%; background: var(--surface-2); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 14px 18px; color: var(--text);
            font-family: var(--font-body); font-size: 16px; outline: none; margin-bottom: 18px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease, background 0.3s ease; resize: vertical;
        }
        textarea::placeholder, input::placeholder { color: rgba(255, 255, 255, 0.32); }
        textarea:focus, input[type="text"]:focus { border-color: var(--accent); background: var(--surface-3); box-shadow: 0 0 0 3px var(--accent-glow); }

        .hp-field { position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; opacity: 0; overflow: hidden; }

        .char-count { text-align: right; font-family: var(--font-mono); font-size: 0.72rem; color: var(--text-muted); margin-top: -12px; margin-bottom: 18px; }

        .btn {
            width: 100%; padding: 16px; border-radius: 999px; font-family: var(--font-display);
            font-size: 0.85rem; font-weight: 600; letter-spacing: 1px; border: none; cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
            display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 14px;
        }
        .btn-primary { background: var(--accent); color: #000000; box-shadow: 0 0 0 1px rgba(71, 165, 255, 0.18), 0 10px 30px rgba(71, 165, 255, 0.16); }
        .btn-primary:hover:not(:disabled) { background: #63b2ff; transform: translateY(-2px); box-shadow: 0 0 0 1px rgba(71, 165, 255, 0.28), 0 16px 38px rgba(71, 165, 255, 0.28); }
        .btn-primary:disabled { opacity: 0.55; cursor: not-allowed; }

        .back-home-link { font-family: var(--font-mono); font-size: 0.78rem; }
        .back-home-link a { color: var(--text-muted); text-decoration: none; transition: color 0.2s ease; }
        .back-home-link a:hover { color: var(--accent); }

        .toast {
            position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%) translateY(20px);
            background: var(--surface-3); border: 1px solid var(--border); border-radius: var(--radius-sm);
            padding: 12px 22px; font-family: var(--font-mono); font-size: 0.8rem; color: var(--text);
            opacity: 0; pointer-events: none; transition: opacity 0.3s ease, transform 0.3s ease; z-index: 50;
            box-shadow: var(--shadow); max-width: 90vw; text-align: center;
        }
        .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
        .toast.success { border-color: rgba(158, 206, 106, 0.4); color: var(--success); }
        .toast.error { border-color: rgba(255, 75, 110, 0.4); color: var(--danger); }
    </style>
</head>
<body>

<?php include 'loading.php'; ?>

<?php include __DIR__ . '/donation_banner.php'; ?>

<main id="main-wrapper" style="display:none;">
    <div class="app-container">
        <div class="login-blob b1"></div>
        <div class="login-blob b2"></div>

        <div class="glass-card">
            <div class="view-label">Talk To Us</div>
            <div class="view-title">SEND FEEDBACK</div>
            <div class="view-desc">Found a bug, or have an idea? Tell us — this goes straight to the team.</div>

            <form id="feedbackForm" autocomplete="off">
                <div class="hp-field" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <textarea id="message" name="message" placeholder="What's on your mind?" rows="6" maxlength="4000" required></textarea>
                <div class="char-count"><span id="charCount">0</span> / 4000</div>

                <div class="cf-turnstile" id="turnstileFeedbackWidget" data-theme="dark" style="margin-bottom: 18px;"></div>

                <button type="submit" class="btn btn-primary" id="btn-send-feedback">✉️ SEND FEEDBACK</button>
            </form>

            <div class="back-home-link"><a href="index.php">← Back to Secret Gate</a></div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<div id="toast" class="toast"></div>

<script nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
    // TRUSTED TYPES – enforce secure DOM policies
    if (window.trustedTypes && trustedTypes.createPolicy) {
        trustedTypes.createPolicy('default', {
            createHTML: (string) => string,
            createScript: (string) => string,
            createScriptURL: (string) => string
        });
    }

    // GLOBAL STATE – API endpoint and security tokens
    const API_BASE = 'feedback-api.php';
    const TURNSTILE_SITE_KEY = "<?php echo htmlspecialchars(getenv('CF_TURNSTILE_SITE_KEY') ?: '', ENT_QUOTES, 'UTF-8'); ?>";
    let turnstileWidgetId = null;
    let csrfToken = '';

    // TOAST NOTIFICATION – transient user feedback
    function showToastMsg(msg, duration = 3000, type = '') {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.className = 'toast show' + (type ? ' ' + type : '');
        clearTimeout(showToastMsg._t);
        showToastMsg._t = setTimeout(() => { toast.className = 'toast'; }, duration);
    }

    // TURNSTILE RENDER – retry until widget library ready
    function renderTurnstileWidget(attemptsLeft = 20) {
        if (turnstileWidgetId !== null) return;
        if (!TURNSTILE_SITE_KEY) return;
        if (typeof turnstile === 'undefined') {
            if (attemptsLeft <= 0) {
                console.error('Turnstile script failed to load in time.');
                return;
            }
            setTimeout(() => renderTurnstileWidget(attemptsLeft - 1), 250);
            return;
        }
        turnstileWidgetId = turnstile.render('#turnstileFeedbackWidget', {
            sitekey: TURNSTILE_SITE_KEY,
            theme: 'dark'
        });
    }

    // CSRF TOKEN – obtain for secure form submission
    async function fetchCsrfToken() {
        try {
            const res = await fetch(`${API_BASE}?action=get_csrf`, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data && data.success) {
                csrfToken = data.csrf_token;
            }
        } catch (e) {
            console.error('Could not fetch CSRF token', e);
        }
    }

    // CHARACTER COUNT – live update as user types
    const messageBox = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    messageBox.addEventListener('input', () => {
        charCount.textContent = messageBox.value.length;
    });

    // FEEDBACK SUBMIT – validate, secure, and send
    document.getElementById('feedbackForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('btn-send-feedback');
        const message = messageBox.value.trim();

        // VALIDATION – check required fields
        if (!message) {
            showToastMsg('Please write a message first.', 2500, 'error');
            return;
        }
        if (!csrfToken) {
            showToastMsg('Still setting things up — try again in a moment.', 2500, 'error');
            await fetchCsrfToken();
            return;
        }

        // TURNSTILE CHECK – ensure human verification
        const turnstileResponse = typeof turnstile !== 'undefined' && turnstileWidgetId !== null
            ? turnstile.getResponse(turnstileWidgetId)
            : '';
        if (!turnstileResponse) {
            showToastMsg('Please complete the verification.', 2500, 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'SENDING...';

        // SUBMIT – send feedback to API
        try {
            const res = await fetch(API_BASE, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({
                    action: 'send_feedback',
                    csrf_token: csrfToken,
                    message: message,
                    website: document.getElementById('website').value,
                    'cf-turnstile-response': turnstileResponse
                })
            });
            const data = await res.json();

            // RESPONSE – handle success or error
            if (data && data.success) {
                showToastMsg('✅ Thanks — feedback sent!', 3000, 'success');
                document.getElementById('feedbackForm').reset();
                charCount.textContent = '0';
                await fetchCsrfToken();
            } else {
                showToastMsg(data && data.error ? data.error : 'Something went wrong.', 3000, 'error');
            }
        } catch (err) {
            console.error(err);
            showToastMsg('Network error. Please try again.', 3000, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = '✉️ SEND FEEDBACK';
            if (typeof turnstile !== 'undefined' && turnstileWidgetId !== null) {
                turnstile.reset(turnstileWidgetId);
            }
        }
    });

    // INIT – start security setup on page load
    document.addEventListener('DOMContentLoaded', function () {
        renderTurnstileWidget();
        fetchCsrfToken();
    });

    // PAGE INIT – reveal content after load to avoid flicker
    window.addEventListener('load', function () {
        setTimeout(function () {
            const loader = document.getElementById('loader-wrapper');
            if (loader) loader.classList.add('hidden');
            const main = document.getElementById('main-wrapper');
            if (main) main.style.display = 'flex';
            if (typeof showDonationBanner === 'function') showDonationBanner();
        }, 1000);
    });

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================

</script>
</body>
</html>