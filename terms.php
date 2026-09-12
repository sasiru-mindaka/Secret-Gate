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

// ACCESS FLAG – signal that this page is loaded legitimately
define('LOADING_ACCESS_ALLOWED', true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#000000">
    <title>Terms and Conditions - Secret Gate</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>

    <link rel="icon" type="image/png" href="image.png">

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

        html { min-height: 100%; background: var(--bg); scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            overflow-x: hidden;
            position: relative;
            line-height: 1.7;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -3;
            pointer-events: none;
            background:
                radial-gradient(circle at 18% 18%, rgba(71,165,255,0.08), transparent 38%),
                radial-gradient(circle at 82% 12%, rgba(255,143,0,0.05), transparent 36%),
                radial-gradient(circle at 50% 90%, rgba(76,80,85,0.10), transparent 42%);
            background-size: 200% 200%;
            animation: ambientDrift 22s ease-in-out infinite;
        }

        @keyframes ambientDrift {
            0%, 100% { background-position: 0% 0%, 100% 0%, 50% 100%; }
            50% { background-position: 20% 30%, 80% 20%, 40% 80%; }
        }

        #star-canvas { position: fixed; inset: 0; width: 100%; height: 100%; z-index: -2; opacity: 0.6; pointer-events: none; }
        .nebula { position: fixed; inset: 0; z-index: -1; background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.02) 0%, transparent 60%); filter: blur(80px); pointer-events: none; }

        #main-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: clamp(22px, 5vw, 48px) clamp(12px, 3vw, 24px) 28px;
            position: relative;
            z-index: 1;
        }

        .app-container { position: relative; width: min(100%, 900px); animation: fadeInUp 0.7s cubic-bezier(0.2,0.8,0.2,1) both 0.1s; }
        .login-blob { position: absolute; border-radius: 50%; filter: blur(72px); opacity: 0.25; pointer-events: none; z-index: -1; }
        .login-blob.b1 { width: 340px; height: 340px; background: radial-gradient(circle, rgba(71,165,255,0.4), transparent 68%); top: -70px; right: -90px; }
        .login-blob.b2 { width: 280px; height: 280px; background: radial-gradient(circle, rgba(255,143,0,0.22), transparent 68%); bottom: 40px; left: -110px; }

        .glass-card {
            position: relative;
            background: rgba(17,19,23,0.96);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: clamp(24px, 5vw, 46px);
            box-shadow: var(--shadow), inset 0 1px 0 rgba(255,255,255,0.04);
            overflow: hidden;
        }
        .glass-card::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(255,255,255,0.035), transparent 28%, transparent 72%, rgba(71,165,255,0.02));
        }

        .policy-head { position: relative; text-align: center; padding-bottom: 28px; margin-bottom: 28px; border-bottom: 1px solid var(--border-soft); }
        .view-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: var(--font-mono);
            font-size: 0.7rem;
            letter-spacing: 2.5px;
            color: var(--amber);
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .view-label::before, .view-label::after { content: ''; width: 14px; height: 1px; background: var(--amber); opacity: 0.6; }
        .view-title {
            font-family: var(--font-display);
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 600;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
            background: linear-gradient(to bottom, #ffffff 0%, #9aa1ab 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .view-desc { font-family: var(--font-mono); font-weight: 300; font-size: 0.78rem; color: var(--text-muted); }

        .policy-section { position: relative; padding: 18px 0 6px; animation: sectionIn 0.55s cubic-bezier(0.2,0.8,0.2,1) both; }
        .policy-section:nth-child(2) { animation-delay: .08s; }
        .policy-section:nth-child(3) { animation-delay: .13s; }
        .policy-section:nth-child(4) { animation-delay: .18s; }
        .policy-section:nth-child(5) { animation-delay: .23s; }
        .policy-section:nth-child(6) { animation-delay: .28s; }
        .policy-section:nth-child(7) { animation-delay: .33s; }
        .policy-section:nth-child(8) { animation-delay: .38s; }
        .policy-section h2 {
            color: var(--text);
            font-size: 1.08rem;
            margin-bottom: 11px;
            border-left: 3px solid var(--accent);
            padding-left: 12px;
            font-weight: 600;
            letter-spacing: -0.01em;
        }
        .policy-section p,
        .policy-section li { color: var(--text-muted); font-size: 0.92rem; }
        .policy-section p { margin-bottom: 12px; }
        .policy-section ul { margin: 0 0 8px 20px; padding: 0; }
        .policy-section li { margin-bottom: 9px; padding-left: 3px; }
        .policy-section strong { color: var(--text); }
        .policy-section a { color: var(--accent); text-decoration: none; }
        .policy-section a:hover { text-decoration: underline; }

        .highlight-box {
            background: rgba(71,165,255,0.07);
            border: 1px solid rgba(71,165,255,0.18);
            border-radius: var(--radius-sm);
            padding: 16px 18px;
            margin: 18px 0 7px;
            font-size: 0.88rem;
            color: var(--text-muted);
        }
        .highlight-box strong { color: var(--accent); }

        .warning-box {
            background: rgba(255,75,110,0.07);
            border: 1px solid rgba(255,75,110,0.20);
            border-radius: var(--radius-sm);
            padding: 16px 18px;
            margin: 18px 0 7px;
            font-size: 0.88rem;
            color: var(--text-muted);
        }
        .warning-box strong { color: var(--danger); }

        .bottom-actions {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 24px;
            padding-top: 22px;
            border-top: 1px solid var(--border-soft);
        }
        .btn-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 11px 18px;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: var(--text);
            background: transparent;
            text-decoration: none;
            font-family: var(--font-display);
            font-size: 0.76rem;
            font-weight: 600;
            letter-spacing: 0.7px;
            transition: transform .25s ease, background .25s ease, border-color .25s ease;
        }
        .btn-link:hover { transform: translateY(-2px); background: var(--surface-3); border-color: var(--accent-soft); }
        .btn-link.primary { background: var(--accent); color: #000; border-color: var(--accent); }
        .btn-link.primary:hover { background: #63b2ff; }

        .feedback-float-btn {
            position: fixed;
            bottom: 22px;
            right: 22px;
            z-index: 1000;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--accent);
            color: #000;
            border: none;
            box-shadow: 0 4px 18px rgba(71,165,255,0.35);
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s cubic-bezier(.2,.9,.4,1), box-shadow .2s ease;
            touch-action: manipulation;
        }
        .feedback-float-btn:hover { transform: scale(1.05); box-shadow: 0 6px 25px rgba(71,165,255,0.5); }
        .feedback-float-btn:active { transform: scale(.92); }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10000;
            background: rgba(0,0,0,.68);
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            width: 100%;
            max-width: 410px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: clamp(20px, 5vw, 32px);
            box-shadow: var(--shadow-hover);
            text-align: left;
            animation: viewFadeIn .35s cubic-bezier(.2,.8,.2,1) both;
        }
        .modal-title { font-family: var(--font-display); font-size: 1.18rem; font-weight: 600; margin-bottom: 8px; color: var(--text); }
        .modal-desc { font-family: var(--font-mono); font-weight: 300; font-size: .76rem; color: var(--text-muted); line-height: 1.55; margin-bottom: 18px; }
        .modal-box textarea {
            width: 100%;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            color: var(--text);
            font-family: var(--font-body);
            font-size: 16px;
            outline: none;
            resize: vertical;
            min-height: 130px;
            transition: border-color .3s ease, box-shadow .3s ease, background .3s ease;
        }
        .modal-box textarea::placeholder { color: rgba(255,255,255,.32); }
        .modal-box textarea:focus { border-color: var(--accent); background: var(--surface-3); box-shadow: 0 0 0 3px var(--accent-glow); }
        .hp-field { position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; opacity: 0; overflow: hidden; }
        .feedback-char-count { text-align: right; font-family: var(--font-mono); font-size: .7rem; color: var(--text-muted); margin-top: 7px; margin-bottom: 16px; }
        .modal-error { font-family: var(--font-mono); font-size: .71rem; color: var(--danger); min-height: 1.2em; margin: 4px 0 10px; }
        .modal-actions { display: flex; gap: 10px; }
        .modal-actions .btn { margin: 0; }
        .btn {
            flex: 1;
            width: 100%;
            padding: 13px 16px;
            border-radius: 999px;
            font-family: var(--font-display);
            font-size: .76rem;
            font-weight: 600;
            letter-spacing: .7px;
            border: 1px solid transparent;
            cursor: pointer;
            min-height: 44px;
            transition: transform .25s ease, box-shadow .25s ease, background .25s ease, border-color .25s ease;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-primary { background: var(--accent); color: #000; box-shadow: 0 0 0 1px rgba(71,165,255,.18), 0 10px 30px rgba(71,165,255,.16); }
        .btn-primary:hover { background: #63b2ff; }
        .btn-outline { background: transparent; border-color: var(--border); color: var(--text); }
        .btn-outline:hover { background: var(--surface-3); border-color: var(--accent-soft); }
        .btn.is-loading { pointer-events: none; opacity: .82; position: relative; color: transparent !important; }
        .btn.is-loading::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 18px; height: 18px;
            margin: -9px 0 0 -9px;
            border: 2px solid rgba(255,255,255,.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9998;
            background: rgba(0,0,0,.7);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 24px;
            padding: 30px;
        }
        .loading-overlay.active { display: flex; }
        .loading-spinner { width: 48px; height: 48px; border: 3px solid var(--border-soft); border-top-color: var(--accent); border-radius: 50%; animation: spin .9s linear infinite; }
        .loading-overlay .label { font-family: var(--font-mono); font-size: .9rem; color: var(--text); letter-spacing: 1.5px; text-align: center; }

        .social-fab-wrap {
            position: fixed;
            left: 18px;
            bottom: 18px;
            z-index: 1000;
            width: 44px;
            height: 44px;
        }
        .social-bar {
            position: absolute;
            inset: 0;
            display: block;
            pointer-events: none;
        }
        .social-bar a {
            position: absolute;
            top: 50%;
            left: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,.14);
            box-shadow: 0 5px 18px rgba(0,0,0,.28);
            opacity: 0;
            transform: translate(-50%, -50%) scale(.3);
            transition: transform .38s cubic-bezier(.34,1.56,.64,1), opacity .22s ease, box-shadow .22s ease, border-color .22s ease;
            transition-delay: 0s;
            touch-action: manipulation;
            outline: none;
        }
        .social-bar a svg { width: 18px; height: 18px; fill: currentColor; display: block; pointer-events: none; }
        .social-bar a:hover, .social-bar a:focus-visible { box-shadow: 0 8px 24px rgba(0,0,0,.4); border-color: rgba(255,255,255,.28); filter: brightness(1.08); }
        .social-bar a:active { transform: translate(calc(-50% + var(--x)), calc(-50% + var(--y))) scale(.94); }
        .social-bar .whatsapp { background: linear-gradient(145deg,#25D366,#128C7E); }
        .social-bar .facebook { background: linear-gradient(145deg,#1877F2,#0D5FCC); }
        .social-bar .instagram { background: linear-gradient(145deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }
        .social-bar .tiktok { background: linear-gradient(145deg,#161616,#000); }
        .social-bar a::after {
            content: attr(aria-label);
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%) translateX(-4px);
            padding: 6px 9px;
            border-radius: 7px;
            background: rgba(17,19,23,.96);
            border: 1px solid var(--border-soft);
            color: var(--text);
            font-family: var(--font-mono);
            font-size: .62rem;
            letter-spacing: .4px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease, transform .18s ease;
        }
        .social-bar a:hover::after, .social-bar a:focus-visible::after { opacity: 1; transform: translateY(-50%) translateX(0); }

        .social-fab-wrap.open .social-bar { pointer-events: auto; }
        .social-fab-wrap.open .social-bar a {
            opacity: .92;
            pointer-events: auto;
            transform: translate(calc(-50% + var(--x)), calc(-50% + var(--y))) scale(1);
        }
        .social-fab-wrap.open .social-bar a:hover,
        .social-fab-wrap.open .social-bar a:focus-visible {
            opacity: 1;
            transform: translate(calc(-50% + var(--x)), calc(-50% + var(--y))) scale(1.08);
        }
        .social-fab-wrap.open .social-bar a:nth-of-type(1) { transition-delay: .02s; }
        .social-fab-wrap.open .social-bar a:nth-of-type(2) { transition-delay: .07s; }
        .social-fab-wrap.open .social-bar a:nth-of-type(3) { transition-delay: .12s; }
        .social-fab-wrap.open .social-bar a:nth-of-type(4) { transition-delay: .17s; }

        .follow-us-btn {
            position: relative;
            z-index: 2;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.14);
            background: linear-gradient(145deg, var(--accent), var(--accent-soft));
            color: #06121f;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 18px rgba(0,0,0,.32);
            transition: transform .22s cubic-bezier(.2,.9,.4,1), box-shadow .22s ease;
            touch-action: manipulation;
            outline: none;
        }
        .follow-us-btn:hover { transform: scale(1.05); box-shadow: 0 8px 24px rgba(0,0,0,.42); }
        .follow-us-btn:active { transform: scale(.94); }
        .follow-us-btn svg {
            width: 19px;
            height: 19px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.2;
            stroke-linecap: round;
            transition: transform .3s ease;
        }
        .social-fab-wrap.open .follow-us-btn svg { transform: rotate(45deg); }
        .follow-us-btn::after {
            content: 'Follow us';
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%) translateX(-4px);
            padding: 6px 9px;
            border-radius: 7px;
            background: rgba(17,19,23,.96);
            border: 1px solid var(--border-soft);
            color: var(--text);
            font-family: var(--font-mono);
            font-size: .62rem;
            letter-spacing: .4px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease, transform .18s ease;
        }
        .follow-us-btn:hover::after, .follow-us-btn:focus-visible::after { opacity: 1; transform: translateY(-50%) translateX(0); }
        .social-fab-wrap.open .follow-us-btn::after { display: none; }

        .toast {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: var(--surface-3);
            border: 1px solid var(--accent-soft);
            border-radius: 999px;
            padding: 14px 28px;
            font-family: var(--font-mono);
            font-size: .8rem;
            color: #fff;
            box-shadow: 0 15px 35px rgba(0,0,0,.5);
            transition: all .4s cubic-bezier(.2,.8,.2,1);
            opacity: 0;
            z-index: 12000;
            max-width: min(92vw,420px);
            width: max-content;
            text-align: center;
        }
        .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        .toast.success { border-color: var(--success); }
        .toast.error { border-color: var(--danger); }

        .cf-turnstile { max-width: 100%; overflow: hidden; margin-left: auto; margin-right: auto; }

        @keyframes fadeInUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        @keyframes sectionIn { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
        @keyframes viewFadeIn { 0% { opacity: 0; transform: translateY(10px) scale(.98); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 700px) {
            .glass-card { border-radius: var(--radius-md); padding: 22px 18px; }
            .login-blob { display: none; }
            .policy-section { padding-top: 15px; }
            .policy-section p, .policy-section li { font-size: .89rem; }
            .bottom-actions { flex-direction: column; }
            .btn-link { width: 100%; }
            .social-fab-wrap { left: 10px; bottom: 14px; width: 40px; height: 40px; }
            .follow-us-btn { width: 40px; height: 40px; }
            .social-bar a { width: 34px; height: 34px; }
            .social-bar a svg { width: 15px; height: 15px; }
            .social-bar a::after, .follow-us-btn::after { display: none; }
            .feedback-float-btn { width: 44px; height: 44px; font-size: 20px; bottom: 16px; right: 16px; }
            .modal-box { padding: 20px; }
            .modal-actions { flex-direction: column; }
        }

        @media (max-width: 380px) {
            #main-wrapper { padding-left: 10px; padding-right: 10px; }
            .glass-card { padding: 18px 13px; border-radius: var(--radius-sm); }
            .view-title { font-size: 1.6rem; }
            .view-desc { font-size: .7rem; }
            .policy-section h2 { font-size: 1rem; }
            .policy-section p, .policy-section li { font-size: .84rem; }
            .social-fab-wrap { left: 8px; bottom: 10px; width: 36px; height: 36px; }
            .follow-us-btn { width: 36px; height: 36px; }
            .social-bar a { width: 30px; height: 30px; }
            .social-bar a svg { width: 13px; height: 13px; }
            .feedback-float-btn { width: 40px; height: 40px; font-size: 18px; bottom: 12px; right: 12px; }
            .toast { padding: 10px 16px; font-size: .7rem; bottom: 20px; }
        }
    </style>
</head>
<body>

<?php include 'loading.php'; ?>

<canvas id="star-canvas"></canvas>
<div class="nebula"></div>

<?php include __DIR__ . '/donation_banner.php'; ?>

<main id="main-wrapper" style="display:none;">
    <div class="app-container">
        <div class="login-blob b1"></div>
        <div class="login-blob b2"></div>

        <article class="glass-card">
            <header class="policy-head">
                <div class="view-label">Legal Terms of Service</div>
                <h1 class="view-title">TERMS &amp; CONDITIONS</h1>
                <p class="view-desc">Effective Date: September 1, 2026</p>
            </header>

            <section class="policy-section">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing or using the <strong>SecretGate</strong> platform (<a href="https://secretgate.site"> <!-- Add your domain here --> secretgate.site</a>), you agree to be bound by these Terms and Conditions. If you do not agree to these terms, you must refrain from using our services immediately.</p>
            </section>

            <section class="policy-section">
                <h2>2. Age Requirement</h2>
                <p>You must be at least 13 years of age (or the minimum legal age required in your jurisdiction) to use SecretGate. By using this service, you represent and warrant that you meet this age requirement.</p>
            </section>

            <section class="policy-section">
                <h2>3. Zero Tolerance Abuse Policy</h2>
                <p>SecretGate provides a secure and private environment for anonymous communication. However, anonymity is not a license for abuse. We strictly prohibit the transmission of harmful or unlawful content.</p>
                <div class="warning-box">
                    <strong>Strictly Prohibited Behavior:</strong> Cyberbullying, harassment, hate speech, explicit/sexual violence, blackmail, extortion, doxxing, identity theft, threats of violence, or any illegal activities are strictly forbidden.
                </div>
                <p>Violation of this policy will result in immediate termination of service access, permanent IP blacklisting, and cooperation with authorities if required by law.</p>
            </section>

            <section class="policy-section">
                <h2>4. User Generated Content &amp; Disclaimer of Liability</h2>
                <p>SecretGate operates as an intermediary infrastructure provider enabling client-side encrypted messaging.</p>
                <ul>
                    <li><strong>No Endorsement:</strong> We do not monitor, generate, endorse, or verify the accuracy of messages transmitted by anonymous senders.</li>
                    <li><strong>User Responsibility:</strong> Senders carry total legal liability for the content they generate and submit through the platform.</li>
                    <li><strong>Limitation of Liability:</strong> Under no circumstances shall SecretGate, its developer, or hosting infrastructure be held liable for any direct, indirect, incidental, or consequential damages resulting from user communications or platform usage.</li>
                </ul>
            </section>

            <section class="policy-section">
                <h2>5. Platform Integrity &amp; System Security</h2>
                <p>Users agree not to exploit or tamper with SecretGate infrastructure:</p>
                <ul>
                    <li>You may not attempt to reverse engineer, crack, or disrupt client-side cryptographic functions.</li>
                    <li>Automated bot submissions, DDoS attacks, scraping, or attempts to bypass rate-limiting controls (including Cloudflare Turnstile defenses) are prohibited.</li>
                </ul>
            </section>

            <section class="policy-section">
                <h2>6. Service Availability &amp; Modifications</h2>
                <p>SecretGate is provided on an &quot;AS IS&quot; and &quot;AS AVAILABLE&quot; basis without warranties of any kind. We reserve the right to modify, suspend, or discontinue any feature or part of the service at any time without prior notice.</p>
            </section>

            <section class="policy-section">
                <h2>7. Reporting Violations</h2>
                <p>If you receive a message that violates these terms or infringes upon your rights, please use the built-in report feature or contact administration through the feedback tool on <a href="https://secretgate.site">secretgate.site</a> <!-- Add your domain here -->
                to request message blacklisting and sender IP blocking.</p>
            </section>

            <section class="policy-section">
                <h2>8. Intellectual Property</h2>
                <p>All brand graphics, custom application design elements, source code, and logo assets of SecretGate belong exclusively to the platform developer.</p>
            </section>

            <div class="bottom-actions">
                <a class="btn-link primary" href="https://secretgate.site">← BACK TO HOME</a> <!-- Add your domain here -->
            </div>
        </article>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<div id="toast" class="toast"></div>

<div class="modal-overlay" id="feedbackModalOverlay">
    <div class="modal-box">
        <div class="modal-title">✉️ Send Feedback</div>
        <div class="modal-desc">Found a bug, or have an idea? Tell us — this goes straight to the team.</div>
        <form id="feedbackForm" autocomplete="off">
            <div class="hp-field" aria-hidden="true">
                <label for="feedbackWebsite">Website</label>
                <input type="text" id="feedbackWebsite" name="website" tabindex="-1" autocomplete="off">
            </div>
            <textarea id="feedbackMessage" name="message" placeholder="What's on your mind?" rows="5" maxlength="4000" required></textarea>
            <div class="feedback-char-count"><span id="feedbackCharCount">0</span> / 4000</div>
            <div class="cf-turnstile" id="turnstileFeedbackWidget" data-theme="dark" style="margin-bottom:18px;"></div>
        </form>
        <div class="modal-error" id="feedbackModalError"></div>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" id="feedbackModalCancel">CANCEL</button>
            <button type="button" class="btn btn-primary" id="feedbackModalSend">✉️ SEND</button>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
    <div class="label">Please wait...</div>
</div>

<div class="social-fab-wrap" id="socialFabWrap">
    <div class="social-bar" id="socialBar" aria-label="Social media links">
        <a href="https://whatsapp.com/channel/0029VbE3tzSCMY0IdZqTq62T" target="_blank" rel="noopener noreferrer" class="whatsapp" aria-label="WhatsApp" tabindex="-1">
            <!-- Add your social media links here -->
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        </a>
        <a href="https://www.facebook.com/secretgate.site" target="_blank" rel="noopener noreferrer" class="facebook" aria-label="Facebook" tabindex="-1">
            <!-- Add your social media links here -->
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>
        <a href="https://www.instagram.com/secretgate.site" target="_blank" rel="noopener noreferrer" class="instagram" aria-label="Instagram" tabindex="-1">
            <!-- Add your social media links here -->
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </a>
        <a href="https://www.tiktok.com/@secretgate.site"  target="_blank" rel="noopener noreferrer" class="tiktok" aria-label="TikTok" tabindex="-1">
            <!-- Add your social media links here -->
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.62v4.08c-1.28.12-2.58-.18-3.68-.71v8.52c0 2.06-.74 4.11-2.24 5.61-1.5 1.49-3.55 2.34-5.66 2.29-2.11-.05-4.12-1-5.55-2.57-1.43-1.57-2.2-3.66-2.1-5.77.1-2.1 1.06-4.11 2.64-5.55 1.58-1.44 3.68-2.16 5.78-1.99v4.1c-.94-.13-1.92.1-2.66.7-.74.6-1.19 1.52-1.2 2.47-.01.95.43 1.87 1.16 2.48.73.61 1.7.85 2.65.73.96-.13 1.82-.6 2.42-1.34.6-.74.88-1.72.75-2.67V.02z"/></svg>
        </a>
    </div>

    <button type="button" class="follow-us-btn" id="followUsBtn" aria-haspopup="true" aria-expanded="false" aria-controls="socialBar" aria-label="Follow us">
        <svg viewBox="0 0 24 24" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    </button>
</div>

<button class="feedback-float-btn" id="feedbackFloatBtn" aria-label="Send feedback">💬</button>

<script nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
(function () {
    'use strict';

    const FEEDBACK_API_BASE = 'feedback-api.php';
    const TURNSTILE_SITE_KEY = "<?php echo htmlspecialchars(getenv('CF_TURNSTILE_SITE_KEY') ?: '', ENT_QUOTES, 'UTF-8'); ?>";
    let feedbackCsrfToken = '';
    let feedbackTurnstileWidgetId = null;
    let toastTimer = null;

    const $ = (id) => document.getElementById(id);

    // TRANSIENT ALERTS – avoid blocking browser dialogs
    function showToastMsg(message, duration = 2800, type = '') {
        const toast = $('toast');
        if (!toast) return;
        toast.textContent = message;
        toast.className = 'toast show' + (type ? ' ' + type : '');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { toast.classList.remove('show'); }, duration);
    }

    // LOADING INDICATOR – prevent double submits
    function setBtnLoading(btn, loading) {
        if (btn) btn.classList.toggle('is-loading', !!loading);
    }

    // TURNSTILE – library may load late, so retry
    function renderFeedbackTurnstileWidget(attemptsLeft = 20) {
        if (feedbackTurnstileWidgetId !== null) return;
        const container = $('turnstileFeedbackWidget');
        if (!container || !TURNSTILE_SITE_KEY) return;
        if (typeof turnstile === 'undefined') {
            if (attemptsLeft <= 0) return;
            setTimeout(() => renderFeedbackTurnstileWidget(attemptsLeft - 1), 250);
            return;
        }
        feedbackTurnstileWidgetId = turnstile.render(container, {
            sitekey: TURNSTILE_SITE_KEY,
            theme: 'dark'
        });
    }

    // CSRF PROTECTION – secure form against cross-site requests
    async function fetchFeedbackCsrfToken() {
        try {
            const resp = await fetch(`${FEEDBACK_API_BASE}?action=get_csrf`, { credentials: 'same-origin' });
            const data = await resp.json();
            if (data && data.success) feedbackCsrfToken = data.csrf_token;
        } catch (e) {
            console.error('Feedback security init failed', e);
        }
    }

    // MODAL OPEN – lazy-load security and focus input
    function openFeedbackModal() {
        const overlay = $('feedbackModalOverlay');
        if (!overlay) return;
        $('feedbackModalError').textContent = '';
        overlay.style.display = 'flex';
        if (!feedbackCsrfToken) fetchFeedbackCsrfToken();
        renderFeedbackTurnstileWidget();
        setTimeout(() => $('feedbackMessage')?.focus(), 120);
    }

    // MODAL CLOSE – hide overlay
    function closeFeedbackModal() {
        const overlay = $('feedbackModalOverlay');
        if (overlay) overlay.style.display = 'none';
    }

    // VISUAL EFFECT – animated background for aesthetics
    function initStars() {
        const canvas = $('star-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let stars = [];

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            const count = window.innerWidth < 700 ? 160 : 350;
            stars = [];
            for (let i = 0; i < count; i++) {
                stars.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    size: Math.random() * 1.4,
                    opacity: Math.random(),
                    speed: Math.random() * 0.018
                });
            }
        }

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (const s of stars) {
                ctx.fillStyle = `rgba(255,255,255,${Math.abs(Math.sin(s.opacity)) * 0.7})`;
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.size, 0, Math.PI * 2);
                ctx.fill();
                s.opacity += s.speed;
            }
            requestAnimationFrame(draw);
        }

        window.addEventListener('resize', resize, { passive: true });
        resize();
        draw();
    }

    // PAGE INIT – reveal content after load to avoid flicker
    window.addEventListener('load', function () {
        setTimeout(function () {
            const loader = $('loader-wrapper');
            if (loader) loader.classList.add('hidden');
            const main = $('main-wrapper');
            if (main) main.style.display = 'flex';
            showDonationBanner();
            fetchFeedbackCsrfToken();
            renderFeedbackTurnstileWidget();
            initSocialFab();
        }, 1000);
    });

    // SOCIAL MENU – radial fan-out with viewport-aware positioning
    function initSocialFab() {
        const wrap = $('socialFabWrap');
        const btn = $('followUsBtn');
        const bar = $('socialBar');
        if (!wrap || !btn || !bar) return;

        const icons = Array.prototype.slice.call(bar.querySelectorAll('a'));
        const margin = 10;
        const desiredRadius = 92;
        const minRadius = 54;

        // DYNAMIC POSITIONING – keep icons within viewport
        function positionIcons() {
            const rect = btn.getBoundingClientRect();
            const iconSize = icons.length ? icons[0].getBoundingClientRect().width || 38 : 38;
            const half = iconSize / 2;

            const spaceRight = window.innerWidth - rect.right + rect.width / 2 - half - margin;
            const spaceUp = rect.top + rect.height / 2 - half - margin;
            const spaceLeft = rect.left + rect.width / 2 - half - margin;

            let startAngle = -90;
            let endAngle = 0;
            let radius = Math.max(minRadius, Math.min(desiredRadius, spaceUp, spaceRight));

            if (spaceRight < minRadius && spaceLeft > spaceRight) {
                startAngle = -180;
                endAngle = -90;
                radius = Math.max(minRadius, Math.min(desiredRadius, spaceUp, spaceLeft));
            }

            icons.forEach(function (icon, i) {
                const angle = icons.length === 1
                    ? (startAngle + endAngle) / 2
                    : startAngle + (endAngle - startAngle) * (i / (icons.length - 1));
                const rad = (angle * Math.PI) / 180;
                const x = Math.cos(rad) * radius;
                const y = Math.sin(rad) * radius;
                icon.style.setProperty('--x', x.toFixed(1) + 'px');
                icon.style.setProperty('--y', y.toFixed(1) + 'px');
            });
        }

        positionIcons();

        // LAYOUT CHANGES – reposition on resize or orientation change
        let resizeTimer = null;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(positionIcons, 120);
        });
        window.addEventListener('orientationchange', function () {
            setTimeout(positionIcons, 200);
        });

        // ACCESSIBILITY – manage focus and aria state
        function setOpen(open) {
            if (open) positionIcons();
            wrap.classList.toggle('open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            icons.forEach(function (icon) {
                icon.setAttribute('tabindex', open ? '0' : '-1');
            });
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(!wrap.classList.contains('open'));
        });

        // DISMISSAL – close on outside click or Escape
        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) setOpen(false);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setOpen(false);
        });
    }

    // EVENT BINDING – attach handlers after DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        initStars();

        // CHARACTER COUNT – live update as user types
        const feedbackMsgBox = $('feedbackMessage');
        const feedbackCharCount = $('feedbackCharCount');
        feedbackMsgBox?.addEventListener('input', () => {
            feedbackCharCount.textContent = feedbackMsgBox.value.length;
        });

        // MODAL TRIGGER – open feedback modal
        $('feedbackFloatBtn')?.addEventListener('click', openFeedbackModal);

        // MODAL DISMISSAL – close on cancel, overlay click, or Escape
        $('feedbackModalCancel')?.addEventListener('click', closeFeedbackModal);
        $('feedbackModalOverlay')?.addEventListener('click', function (e) {
            if (e.target === this) closeFeedbackModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeFeedbackModal();
        });

        // SECURE SUBMISSION – validate and protect feedback
        $('feedbackModalSend')?.addEventListener('click', async function () {
            const sendBtn = this;
            const errorEl = $('feedbackModalError');
            const message = (feedbackMsgBox?.value || '').trim();
            errorEl.textContent = '';

            if (!message) {
                errorEl.textContent = 'Please write a message first.';
                feedbackMsgBox?.focus();
                return;
            }
            if (!feedbackCsrfToken) {
                errorEl.textContent = 'Still setting things up — try again in a moment.';
                await fetchFeedbackCsrfToken();
                return;
            }

            const turnstileResponse = (typeof turnstile !== 'undefined' && feedbackTurnstileWidgetId !== null)
                ? turnstile.getResponse(feedbackTurnstileWidgetId)
                : '';

            if (TURNSTILE_SITE_KEY && !turnstileResponse) {
                errorEl.textContent = 'Please complete the verification.';
                return;
            }

            setBtnLoading(sendBtn, true);
            try {
                const res = await fetch(FEEDBACK_API_BASE, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'send_feedback',
                        csrf_token: feedbackCsrfToken,
                        message: message,
                        website: $('feedbackWebsite').value,
                        'cf-turnstile-response': turnstileResponse
                    })
                });

                const data = await res.json();
                if (data && data.success) {
                    showToastMsg('✅ Thanks — feedback sent!', 3000, 'success');
                    $('feedbackForm').reset();
                    feedbackCharCount.textContent = '0';
                    if (typeof turnstile !== 'undefined' && feedbackTurnstileWidgetId !== null) {
                        try { turnstile.reset(feedbackTurnstileWidgetId); } catch (e) {}
                    }
                    await fetchFeedbackCsrfToken();
                    closeFeedbackModal();
                } else {
                    errorEl.textContent = (data && data.error) ? data.error : 'Something went wrong.';
                }
            } catch (err) {
                console.error(err);
                errorEl.textContent = 'Network error. Please try again.';
            } finally {
                setBtnLoading(sendBtn, false);
            }
        });
    });
})();

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================
</script>
</body>
</html>