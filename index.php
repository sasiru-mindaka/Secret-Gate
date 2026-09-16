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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Gate</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Void Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Cloudflare Turnstile — explicit render -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>

    <link rel="icon" type="image/png" href="./images/secret_gate_logo.png">

    <style nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">

        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
        }

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

        html {
        font-size: 16px;
        height: 100%;
        height: 100dvh;
        overflow: hidden;
        }

        body {
        height: 100%;
        height: 100dvh;
        background: var(--bg);
        color: var(--text);
        font-family: var(--font-body);
        overflow: hidden;
        overscroll-behavior: none;
        display: flex;
        flex-direction: column;
        position: relative;
        }

        body::before {
        content: '';
        position: fixed;
        inset: 0;
        z-index: -3;
        pointer-events: none;
        background:
        radial-gradient(circle at 18% 18%, rgba(71, 165, 255, 0.08), transparent 38%),
        radial-gradient(circle at 82% 12%, rgba(255, 143, 0, 0.05), transparent 36%),
        radial-gradient(circle at 50% 90%, rgba(76, 80, 85, 0.1), transparent 42%);
        background-size: 200% 200%;
        animation: ambientDrift 22s ease-in-out infinite;
        }

        @keyframes ambientDrift {
        0%, 100% {
        background-position: 0% 0%, 100% 0%, 50% 100%;
        }
        50% {
        background-position: 20% 30%, 80% 20%, 40% 80%;
        }
        }

        #star-canvas {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
        opacity: 0.6;
        }

        .nebula {
        position: fixed;
        inset: 0;
        z-index: -1;
        background: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.02) 0%, transparent 60%);
        filter: blur(80px);
        pointer-events: none;
        }

        #main-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
        padding: clamp(12px, 3vw, 24px);
        position: relative;
        z-index: 1;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
        }

        #main-wrapper::-webkit-scrollbar {
        display: none;
        }

        .login-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(72px);
        opacity: 0.25;
        pointer-events: none;
        z-index: 0;
        }

        .login-blob.b1 {
        width: 340px;
        height: 340px;
        background: radial-gradient(circle, rgba(71, 165, 255, 0.4), transparent 68%);
        top: -50px;
        right: -80px;
        }

        .login-blob.b2 {
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(255, 143, 0, 0.22), transparent 68%);
        bottom: -50px;
        left: -80px;
        }

        @media (max-width: 480px) {
        .login-blob {
        display: none;
        }
        }

        .app-container {
        position: relative;
        width: 100%;
        max-width: 500px;
        z-index: 1;
        animation: fadeInUp 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) both 0.1s;
        }

        .glass-card {
        position: relative;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: clamp(24px, 4vw, 44px) clamp(18px, 4vw, 40px);
        box-shadow: var(--shadow), inset 0 1px 0 rgba(255, 255, 255, 0.04);
        transition: border-color 0.4s ease, box-shadow 0.4s ease;
        text-align: center;
        display: flex;
        flex-direction: column;
        max-height: 85vh;
        }

        .glass-card:hover {
        border-color: rgba(71, 165, 255, 0.3);
        box-shadow: var(--shadow-hover), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        #app {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        }

        #app > div {
        display: none;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        }

        #app > div[style*="display: flex"] {
        display: flex !important;
        }

        #app > div {
        animation: viewFadeIn 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        @keyframes viewFadeIn {
        0% {
        opacity: 0;
        transform: translateY(12px) scale(0.98);
        }
        100% {
        opacity: 1;
        transform: translateY(0) scale(1);
        }
        }

        @keyframes fadeInUp {
        0% {
        opacity: 0;
        transform: translateY(20px);
        }
        100% {
        opacity: 1;
        transform: translateY(0);
        }
        }

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
        align-self: center;
        width: fit-content;
        }

        .view-label::before,
        .view-label::after {
        content: '';
        width: 14px;
        height: 1px;
        background: var(--amber);
        opacity: 0.6;
        }

        .view-title {
        font-family: var(--font-display);
        font-size: clamp(1.4rem, 4.2vw, 2rem);
        font-weight: 600;
        letter-spacing: -0.02em;
        margin-bottom: 6px;
        background: linear-gradient(to bottom, #ffffff 0%, #9aa1ab 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        }

        .view-desc {
        font-family: var(--font-mono);
        font-weight: 300;
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 24px;
        }

        textarea,
        input[type="text"] {
        width: 100%;
        max-width: 100%;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 14px 18px;
        color: var(--text);
        font-family: var(--font-body);
        font-size: 16px;
        outline: none;
        margin-bottom: 18px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        resize: vertical;
        }

        textarea::placeholder,
        input::placeholder {
        color: rgba(255, 255, 255, 0.32);
        }

        textarea:focus,
        input[type="text"]:focus {
        border-color: var(--accent);
        background: var(--surface-3);
        box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .url-display {
        background: var(--surface-2);
        border: 1px dashed var(--border-soft);
        border-radius: var(--radius-sm);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        margin-bottom: 22px;
        transition: border-color 0.3s ease, background 0.3s ease;
        }

        .url-display:hover {
        border-color: var(--accent);
        background: var(--surface-3);
        }

        .url-display input {
        background: transparent;
        border: none;
        padding: 0;
        margin: 0;
        font-family: var(--font-mono);
        font-size: 0.75rem;
        color: var(--accent);
        cursor: pointer;
        box-shadow: none;
        outline: none;
        }

        .copy-badge {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1px;
        color: var(--text-muted);
        background: var(--surface-3);
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid var(--border-soft);
        transition: all 0.2s;
        flex-shrink: 0;
        }

        .url-display:hover .copy-badge {
        background: var(--accent);
        color: #000;
        border-color: var(--accent);
        }

        .btn {
        width: 100%;
        padding: 16px;
        border-radius: 999px;
        font-family: var(--font-display);
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 1px;
        border: none;
        cursor: pointer;
        transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 14px;
        min-height: 48px;
        touch-action: manipulation;
        }

        .btn-primary {
        background: var(--accent);
        color: #000000;
        box-shadow: 0 0 0 1px rgba(71, 165, 255, 0.18), 0 10px 30px rgba(71, 165, 255, 0.16);
        }

        .btn-primary:hover {
        background: #63b2ff;
        transform: translateY(-2px);
        box-shadow: 0 0 0 1px rgba(71, 165, 255, 0.28), 0 16px 38px rgba(71, 165, 255, 0.28);
        }

        .btn-outline {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-outline:hover {
        background: var(--surface-3);
        border-color: var(--accent-soft);
        transform: translateY(-2px);
        }

        .btn-danger {
        background: rgba(255, 75, 110, 0.08);
        border: 1px solid rgba(255, 75, 110, 0.25);
        color: var(--danger);
        }

        .btn-danger:hover {
        background: rgba(255, 75, 110, 0.15);
        border-color: var(--danger);
        transform: translateY(-2px);
        }

        .btn-sm {
        width: auto;
        padding: 10px 20px;
        font-size: 0.75rem;
        min-height: 44px;
        }

        .insta-btn {
        width: 100%;
        padding: 16px;
        border-radius: 999px;
        font-family: var(--font-display);
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 1px;
        cursor: pointer;
        margin-bottom: 14px;
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        color: white;
        border: none;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        min-height: 48px;
        }

        .insta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(220, 39, 67, 0.25);
        }

        .divider {
        height: 1px;
        background: var(--border-soft);
        margin: 24px 0;
        }

        .send-to-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(158, 206, 106, 0.08);
        border: 1px solid rgba(158, 206, 106, 0.25);
        border-radius: 60px;
        padding: 6px 20px;
        font-family: var(--font-mono);
        font-size: 0.75rem;
        color: var(--success);
        margin-bottom: 24px;
        letter-spacing: 1px;
        }

        .message-box-wrap {
        position: relative;
        margin-bottom: 18px;
        }

        .message-box-wrap textarea {
        margin-bottom: 0;
        padding-right: 54px;
        }

        .prompt-random-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--surface-3);
        border: 1px solid var(--border-soft);
        border-radius: 50%;
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
        color: var(--accent);
        padding: 0;
        transition: background 0.2s ease, border-color 0.2s ease, transform 0.15s ease;
        }

        .prompt-random-btn:hover {
        background: rgba(71, 165, 255, 0.15);
        border-color: var(--accent);
        }

        .prompt-random-btn:active {
        transform: translateY(1px) scale(0.92) rotate(20deg);
        }

        /* ===== INBOX VIEW - HIGHLY OPTIMIZED ===== */
        #view-inbox {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        }

        .inbox-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 10px;
        text-align: left;
        flex-shrink: 0;
        }

        .inbox-header .view-title {
        margin-bottom: 0;
        font-size: clamp(1.2rem, 3.5vw, 1.6rem);
        }

        .inbox-header .view-label {
        margin-bottom: 2px;
        font-size: 0.65rem;
        }

        .inbox-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        }

        #btn-refresh-inbox {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        line-height: 1;
        }

        #messagesContainer {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
        padding-right: 4px;
        margin-right: -4px;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: var(--accent-soft) transparent;
        -webkit-overflow-scrolling: touch;
        }

        #messagesContainer::-webkit-scrollbar {
        width: 4px;
        }

        #messagesContainer::-webkit-scrollbar-track {
        background: transparent;
        }

        #messagesContainer::-webkit-scrollbar-thumb {
        background: var(--accent-soft);
        border-radius: 10px;
        }

        #messagesContainer::-webkit-scrollbar-thumb:hover {
        background: var(--accent);
        }

        .message-card {
        background: var(--surface-2);
        border: 1px solid var(--border-soft);
        border-left: 3px solid var(--accent);
        border-radius: var(--radius-sm);
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.25s ease;
        text-align: left;
        word-wrap: break-word;
        overflow-wrap: anywhere;
        }

        .message-card:last-child {
        margin-bottom: 4px;
        }

        .message-card:hover {
        border-color: var(--accent-soft);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        transform: translateY(-1px);
        }

        .message-text {
        font-size: clamp(0.9rem, 2.5vw, 0.95rem);
        line-height: 1.6;
        color: var(--text);
        white-space: pre-wrap;
        word-break: break-word;
        }

        .message-text.truncated {
        max-height: 5.6em;
        overflow: hidden;
        position: relative;
        -webkit-mask-image: linear-gradient(to bottom, #000 65%, transparent 100%);
        mask-image: linear-gradient(to bottom, #000 65%, transparent 100%);
        }

        .show-more-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 6px 0;
        margin-top: 6px;
        font-family: var(--font-mono);
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: var(--accent);
        display: inline-block;
        touch-action: manipulation;
        }

        .show-more-btn:hover {
        color: #63b2ff;
        text-decoration: underline;
        }

        .show-more-btn:active {
        transform: scale(0.96);
        }

        .message-time {
        font-family: var(--font-mono);
        font-size: 0.6rem;
        color: var(--text-muted);
        margin-top: 12px;
        letter-spacing: 0.5px;
        }

        .message-actions {
        margin-top: 14px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        }

        .message-actions .btn {
        margin-bottom: 0;
        flex: 1;
        min-width: 100px;
        font-size: 0.75rem;
        padding: 12px 10px;
        min-height: 44px;
        }

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
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        color: #fff;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
        opacity: 0;
        z-index: 9999;
        max-width: min(92vw, 420px);
        width: max-content;
        white-space: normal;
        text-align: center;
        }

        .toast.show {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
        }

        .toast.loading {
        border-color: var(--amber);
        background: var(--surface-2);
        }

        .toast.success {
        border-color: var(--success);
        background: var(--surface-2);
        }

        .toast.error {
        border-color: var(--danger);
        background: var(--surface-2);
        }

        .loading,
        .empty-msg {
        font-family: var(--font-mono);
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
        font-size: 0.85rem;
        }

        .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.65);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        padding: 20px;
        }

        .modal-overlay.active {
        display: flex;
        }

        #pwModalOverlay {
        z-index: 10001;
        }

        .modal-box {
        width: 100%;
        max-width: 380px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 5vw, 32px);
        box-shadow: var(--shadow-hover);
        text-align: left;
        animation: viewFadeIn 0.35s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        max-height: 88vh;
        max-height: 88dvh;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: var(--accent-soft) transparent;
        }

        .modal-box::-webkit-scrollbar {
        width: 5px;
        }

        .modal-box::-webkit-scrollbar-track {
        background: transparent;
        }

        .modal-box::-webkit-scrollbar-thumb {
        background: var(--accent-soft);
        border-radius: 10px;
        }

        .modal-title {
        font-family: var(--font-display);
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text);
        }

        .modal-desc {
        font-family: var(--font-mono);
        font-weight: 300;
        font-size: 0.78rem;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 18px;
        }

        .modal-input-wrap {
        position: relative;
        margin-bottom: 8px;
        }

        .modal-input-wrap input {
        width: 100%;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 14px 46px 14px 18px;
        color: var(--text);
        font-family: var(--font-body);
        font-size: 16px;
        outline: none;
        transition: border-color 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        box-sizing: border-box;
        display: block;
        }

        .modal-input-wrap input:focus {
        border-color: var(--accent);
        background: var(--surface-3);
        box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .modal-toggle-visibility {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        color: var(--text-muted);
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease, transform 0.2s ease;
        border-radius: 6px;
        z-index: 2;
        }

        .modal-toggle-visibility:hover {
        color: var(--accent);
        background: rgba(255, 255, 255, 0.05);
        }

        .modal-toggle-visibility svg {
        width: 20px;
        height: 20px;
        display: block;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
        flex-shrink: 0;
        }

        .pw-remember-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 6px 0 14px;
            font-family: var(--font-mono);
            font-size: 0.76rem;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            transition: color 0.2s ease;
        }
        .pw-remember-wrap:hover {
            color: var(--text);
        }
        .pw-remember-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
            flex-shrink: 0;
            margin: 0;
        }

        .modal-error {
        font-family: var(--font-mono);
        font-size: 0.72rem;
        color: var(--danger);
        min-height: 1.2em;
        margin-bottom: 12px;
        }

        .modal-actions {
        display: flex;
        gap: 10px;
        }

        .modal-actions .btn {
        margin-bottom: 0;
        min-height: 44px;
        }

        /* Recovery modal */
        .recovery-code-box {
        background: var(--surface-2);
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-sm);
        padding: 18px;
        margin: 14px 0 20px 0;
        word-break: break-all;
        font-family: var(--font-mono);
        font-size: 0.7rem;
        color: var(--accent);
        max-height: 160px;
        overflow-y: auto;
        user-select: all;
        line-height: 1.7;
        scrollbar-width: thin;
        scrollbar-color: var(--accent-soft) transparent;
        }

        .recovery-code-box::-webkit-scrollbar {
        width: 6px;
        }

        .recovery-code-box::-webkit-scrollbar-track {
        background: transparent;
        }

        .recovery-code-box::-webkit-scrollbar-thumb {
        background: var(--accent-soft);
        border-radius: 10px;
        }

        .recovery-warning {
        font-family: var(--font-mono);
        font-size: 0.75rem;
        color: var(--amber);
        background: rgba(255, 143, 0, 0.08);
        border: 1px solid rgba(255, 143, 0, 0.2);
        border-radius: var(--radius-sm);
        padding: 12px 16px;
        margin-bottom: 18px;
        line-height: 1.6;
        text-align: left;
        }

        .recovery-warning strong {
        color: #fff;
        }

        /* Auto-delete settings modal */
        .modal-box-wide {
        max-width: 460px;
        padding: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 88vh;
        max-height: 88dvh;
        }

        .modal-box-wide .modal-title,
        .modal-box-wide .modal-desc {
        padding-left: clamp(20px, 5vw, 32px);
        padding-right: clamp(20px, 5vw, 32px);
        }

        .modal-box-wide .modal-title {
        padding-top: clamp(20px, 5vw, 32px);
        margin-bottom: 8px;
        }

        .modal-scroll-body {
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        flex: 1;
        min-height: 0;
        padding: 0 clamp(20px, 5vw, 32px);
        scrollbar-width: thin;
        scrollbar-color: var(--accent-soft) transparent;
        }

        .modal-scroll-body::-webkit-scrollbar {
        width: 5px;
        }

        .modal-scroll-body::-webkit-scrollbar-track {
        background: transparent;
        }

        .modal-scroll-body::-webkit-scrollbar-thumb {
        background: var(--accent-soft);
        border-radius: 10px;
        }

        .modal-sticky-footer {
        flex-shrink: 0;
        padding: 14px clamp(20px, 5vw, 32px) clamp(20px, 5vw, 32px);
        border-top: 1px solid var(--border-soft);
        background: var(--surface);
        }

        .modal-sticky-footer .modal-error {
        margin-bottom: 10px;
        }

        .tmr-section {
        margin-bottom: 22px;
        }

        .tmr-section:last-of-type {
        margin-bottom: 8px;
        }

        .tmr-section-label {
        font-family: var(--font-display);
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--accent);
        margin-bottom: 8px;
        display: flex;
        align-items: baseline;
        gap: 7px;
        flex-wrap: wrap;
        }

        .tmr-section-hint {
        font-family: var(--font-mono);
        font-size: 0.63rem;
        font-weight: 400;
        color: var(--text-muted);
        }

        .tmr-radio-group {
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-sm);
        overflow: hidden;
        background: var(--surface-2);
        }

        .tmr-radio-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        cursor: pointer;
        border-bottom: 1px solid var(--border-soft);
        position: relative;
        transition: background 0.25s ease;
        }

        .tmr-radio-row:last-child {
        border-bottom: none;
        }

        .tmr-radio-row:hover {
        background: var(--surface-3);
        }

        .tmr-radio-row.is-checked {
        background: rgba(71, 165, 255, 0.08);
        }

        .tmr-radio-row input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
        }

        .tmr-radio-dot {
        width: 19px;
        height: 19px;
        border-radius: 50%;
        border: 2px solid var(--border);
        flex-shrink: 0;
        position: relative;
        transition: border-color 0.25s ease, transform 0.2s ease;
        }

        .tmr-radio-row.is-checked .tmr-radio-dot {
        border-color: var(--accent);
        transform: scale(1.05);
        }

        .tmr-radio-dot::after {
        content: '';
        position: absolute;
        inset: 3.5px;
        border-radius: 50%;
        background: var(--accent);
        transform: scale(0);
        transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .tmr-radio-row.is-checked .tmr-radio-dot::after {
        transform: scale(1);
        }

        .tmr-radio-text {
        font-family: var(--font-body);
        font-size: 0.85rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        }

        .tmr-section-note {
        font-family: var(--font-mono);
        font-size: 0.67rem;
        color: var(--text-muted);
        line-height: 1.55;
        margin-top: 9px;
        }

        .tmr-account-card {
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-sm);
        background: var(--surface-2);
        overflow: hidden;
        }

        .tmr-account-title {
        font-family: var(--font-display);
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--accent);
        padding: 14px 14px 2px;
        }

        .tmr-account-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        cursor: pointer;
        transition: background 0.25s ease;
        }

        .tmr-account-row:hover {
        background: var(--surface-3);
        }

        .tmr-account-row-label {
        font-family: var(--font-body);
        font-size: 0.85rem;
        color: var(--text);
        }

        .tmr-account-row-value {
        display: flex;
        align-items: center;
        gap: 4px;
        font-family: var(--font-mono);
        font-size: 0.8rem;
        color: var(--accent);
        }

        .tmr-account-chevron {
        display: inline-block;
        color: var(--text-muted);
        transition: transform 0.25s ease;
        }

        .tmr-account-options {
        display: none;
        border: none;
        border-radius: 0;
        background: transparent;
        border-top: 1px solid var(--border-soft);
        }

        .tmr-account-card.expanded .tmr-account-options {
        display: block;
        }

        .tmr-account-card.expanded .tmr-account-chevron {
        transform: rotate(90deg);
        }

        @keyframes adSavedPop {
        0% {
        transform: scale(0.85);
        opacity: 0;
        }
        60% {
        transform: scale(1.06);
        opacity: 1;
        }
        100% {
        transform: scale(1);
        }
        }

        .tmr-saved-flash {
        animation: adSavedPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @media (max-width: 420px) {
        .tmr-radio-row {
        padding: 11px 12px;
        gap: 10px;
        }
        .tmr-radio-text {
        font-size: 0.78rem;
        }
        }

        .btn.is-loading {
        pointer-events: none;
        opacity: 0.85;
        position: relative;
        color: transparent !important;
        }

        .btn.is-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 18px;
        height: 18px;
        margin: -9px 0 0 -9px;
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        }

        .loading-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9998;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 24px;
        padding: 30px;
        }

        .loading-overlay.active {
        display: flex;
        }

        .loading-spinner {
        width: 48px;
        height: 48px;
        border: 3px solid var(--border-soft);
        border-top-color: var(--accent);
        border-radius: 50%;
        animation: spin 0.9s linear infinite;
        }

        @keyframes spin {
        to {
        transform: rotate(360deg);
        }
        }

        .loading-overlay .label {
        font-family: var(--font-mono);
        font-size: 0.9rem;
        color: var(--text);
        letter-spacing: 1.5px;
        text-align: center;
        }

        .url-display input {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1;
        }

        .cf-turnstile {
        max-width: 100%;
        overflow: hidden;
        margin-left: auto;
        margin-right: auto;
        }

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
        color: #ffffff;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.14);
        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.28);
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.3);
        transition: transform 0.38s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        transition-delay: 0s;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        outline: none;
        }

        .social-bar a svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
        display: block;
        pointer-events: none;
        }

        .social-bar a:hover,
        .social-bar a:focus-visible {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        border-color: rgba(255, 255, 255, 0.28);
        filter: brightness(1.08);
        }

        .social-bar a:active {
        transform: translate(calc(-50% + var(--x)), calc(-50% + var(--y))) scale(0.94);
        }

        .social-bar .whatsapp {
        background: linear-gradient(145deg, #25D366, #128C7E);
        }

        .social-bar .facebook {
        background: linear-gradient(145deg, #1877F2, #0D5FCC);
        }

        .social-bar .instagram {
        background: linear-gradient(145deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        }

        .social-bar .tiktok {
        background: linear-gradient(145deg, #161616, #000000);
        }

        .social-bar a::after {
        content: attr(aria-label);
        position: absolute;
        left: calc(100% + 10px);
        top: 50%;
        transform: translateY(-50%) translateX(-4px);
        padding: 6px 9px;
        border-radius: 7px;
        background: rgba(17, 19, 23, 0.96);
        border: 1px solid var(--border-soft);
        color: var(--text);
        font-family: var(--font-mono);
        font-size: 0.62rem;
        letter-spacing: 0.4px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.18s ease, transform 0.18s ease;
        }

        .social-bar a:hover::after,
        .social-bar a:focus-visible::after {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
        }

        .social-fab-wrap.open .social-bar {
        pointer-events: auto;
        }

        .social-fab-wrap.open .social-bar a {
        opacity: 0.92;
        pointer-events: auto;
        transform: translate(calc(-50% + var(--x)), calc(-50% + var(--y))) scale(1);
        }

        .social-fab-wrap.open .social-bar a:hover,
        .social-fab-wrap.open .social-bar a:focus-visible {
        opacity: 1;
        transform: translate(calc(-50% + var(--x)), calc(-50% + var(--y))) scale(1.08);
        }

        .social-fab-wrap.open .social-bar a:nth-of-type(1) {
        transition-delay: 0.02s;
        }

        .social-fab-wrap.open .social-bar a:nth-of-type(2) {
        transition-delay: 0.07s;
        }

        .social-fab-wrap.open .social-bar a:nth-of-type(3) {
        transition-delay: 0.12s;
        }

        .social-fab-wrap.open .social-bar a:nth-of-type(4) {
        transition-delay: 0.17s;
        }

        .follow-us-btn {
        position: relative;
        z-index: 2;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: linear-gradient(145deg, var(--accent), var(--accent-soft));
        color: #06121f;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.32);
        transition: transform 0.22s cubic-bezier(0.2, 0.9, 0.4, 1), box-shadow 0.22s ease;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        outline: none;
        }

        .follow-us-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.42);
        }

        .follow-us-btn:active {
        transform: scale(0.94);
        }

        .follow-us-btn svg {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.2;
        stroke-linecap: round;
        transition: transform 0.3s ease;
        }

        .social-fab-wrap.open .follow-us-btn svg {
        transform: rotate(45deg);
        }

        .follow-us-btn::after {
        content: 'Follow us';
        position: absolute;
        left: calc(100% + 10px);
        top: 50%;
        transform: translateY(-50%) translateX(-4px);
        padding: 6px 9px;
        border-radius: 7px;
        background: rgba(17, 19, 23, 0.96);
        border: 1px solid var(--border-soft);
        color: var(--text);
        font-family: var(--font-mono);
        font-size: 0.62rem;
        letter-spacing: 0.4px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.18s ease, transform 0.18s ease;
        }

        .follow-us-btn:hover::after,
        .follow-us-btn:focus-visible::after {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
        }

        .social-fab-wrap.open .follow-us-btn::after {
        display: none;
        }

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
        box-shadow: 0 4px 18px rgba(71, 165, 255, 0.35);
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s cubic-bezier(0.2, 0.9, 0.4, 1), box-shadow 0.2s ease, opacity 0.2s ease;
        text-decoration: none;
        touch-action: manipulation;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
        outline: none;
        }

        .feedback-float-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 25px rgba(71, 165, 255, 0.5);
        }

        .feedback-float-btn:active {
        transform: scale(0.92);
        box-shadow: 0 2px 10px rgba(71, 165, 255, 0.3);
        }

        /* Secure link reminder popup */
        .secure-reminder {
        text-align: center;
        max-width: 390px;
        position: relative;
        }

        .modal-box.secure-reminder {
        animation: viewFadeIn 0.35s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        border: 2px solid #ff3b3b;
        }

        .secure-reminder .attention {
        color: var(--amber);
        font-family: var(--font-mono);
        font-size: 0.72rem;
        letter-spacing: 1.6px;
        margin-bottom: 10px;
        display: inline-block;
        animation: attentionPulse 1.1s ease-in-out infinite, attentionShake 0.5s ease-in-out 1;
        }

        @keyframes attentionPulse {
        0%, 100% {
        opacity: 1;
        transform: scale(1);
        }
        50% {
        opacity: 0.6;
        transform: scale(1.05);
        }
        }

        @keyframes attentionShake {
        0%, 100% {
        transform: translateX(0);
        }
        20% {
        transform: translateX(-4px);
        }
        40% {
        transform: translateX(4px);
        }
        60% {
        transform: translateX(-3px);
        }
        80% {
        transform: translateX(3px);
        }
        }

        .secure-reminder-list {
        margin: 16px 0 18px;
        padding: 0;
        list-style: none;
        text-align: left;
        }

        .secure-reminder-list li {
        background: var(--surface-2);
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 9px;
        font-family: var(--font-mono);
        font-size: 0.73rem;
        color: var(--text);
        }

        .secure-reminder-list strong {
        color: var(--accent);
        }

        .secure-reminder-count {
        font-family: var(--font-mono);
        font-size: 0.68rem;
        color: var(--text-muted);
        text-align: center;
        }

        .secure-reminder-progress {
        width: 100%;
        height: 4px;
        background: var(--surface-3);
        border-radius: 999px;
        overflow: hidden;
        margin-top: 9px;
        }

        .secure-reminder-progress span {
        display: block;
        width: 100%;
        height: 100%;
        background: var(--amber);
        transform-origin: left;
        }

        @media (max-width: 600px) {
        .glass-card {
        padding: 20px 16px;
        border-radius: var(--radius-md);
        max-height: 88vh;
        }
        #main-wrapper {
        padding: 12px;
        }
        .btn {
        padding: 14px;
        font-size: 0.8rem;
        min-height: 44px;
        }
        .btn-sm {
        padding: 8px 14px;
        font-size: 0.7rem;
        min-height: 38px;
        }

        .social-fab-wrap {
        left: 10px;
        bottom: 14px;
        width: 40px;
        height: 40px;
        }
        .follow-us-btn {
        width: 40px;
        height: 40px;
        }
        .social-bar a {
        width: 34px;
        height: 34px;
        }
        .social-bar a svg {
        width: 15px;
        height: 15px;
        }
        .social-bar a::after,
        .follow-us-btn::after {
        display: none;
        }

        .feedback-float-btn {
        width: 44px;
        height: 44px;
        font-size: 20px;
        bottom: 16px;
        right: 16px;
        box-shadow: 0 3px 12px rgba(71, 165, 255, 0.3);
        }

        .message-actions .btn {
        flex: 1 1 100%;
        min-width: 100%;
        font-size: 0.7rem;
        padding: 10px;
        }
        .message-card {
        padding: 14px;
        }
        .message-text {
        font-size: 0.9rem;
        }
        .inbox-header .view-title {
        font-size: 1.2rem;
        }

        .modal-box {
        padding: 20px;
        }
        .modal-box-wide {
        padding: 0;
        max-height: 92vh;
        max-height: 92dvh;
        }
        .modal-box-wide .modal-title {
        padding-top: 20px;
        }
        .modal-box-wide .modal-title,
        .modal-box-wide .modal-desc {
        padding-left: 20px;
        padding-right: 20px;
        }
        .modal-scroll-body {
        padding-left: 20px;
        padding-right: 20px;
        }
        .modal-sticky-footer {
        padding: 12px 20px 20px;
        }
        .modal-actions {
        flex-direction: column;
        }
        .modal-actions .btn {
        min-height: 44px;
        }
        }

        @media (max-width: 380px) {
        .glass-card {
        padding: 16px 12px;
        border-radius: var(--radius-sm);
        }
        .view-title {
        font-size: 1.2rem;
        }
        .view-desc {
        font-size: 0.72rem;
        }
        .toast {
        padding: 10px 16px;
        font-size: 0.7rem;
        bottom: 20px;
        }
        .send-to-chip {
        font-size: 0.65rem;
        padding: 4px 12px;
        }
        .recovery-code-box {
        font-size: 0.6rem;
        max-height: 120px;
        }
        .secure-reminder-list li {
        font-size: 0.68rem;
        padding: 10px 12px;
        }
        .secure-reminder .modal-title {
        font-size: 1.05rem;
        }
        .social-fab-wrap {
        left: 8px;
        bottom: 10px;
        width: 36px;
        height: 36px;
        }
        .follow-us-btn {
        width: 36px;
        height: 36px;
        }
        .social-bar a {
        width: 30px;
        height: 30px;
        }
        .social-bar a svg {
        width: 13px;
        height: 13px;
        }
        .feedback-float-btn {
        width: 40px;
        height: 40px;
        font-size: 18px;
        bottom: 12px;
        right: 12px;
        }
        }

        /* Landscape phone support */
        @media (max-height: 500px) and (orientation: landscape) {
        .glass-card {
        max-height: 92vh;
        padding: 16px;
        }
        .view-desc {
        margin-bottom: 12px;
        font-size: 0.7rem;
        }
        .btn {
        padding: 10px;
        font-size: 0.7rem;
        min-height: 36px;
        margin-bottom: 8px;
        }
        .message-card {
        padding: 10px;
        margin-bottom: 8px;
        }
        #messagesContainer {
        max-height: 40vh;
        }
        .social-fab-wrap {
        left: 8px;
        bottom: 8px;
        width: 32px;
        height: 32px;
        }
        .follow-us-btn {
        width: 32px;
        height: 32px;
        }
        .social-bar a {
        width: 28px;
        height: 28px;
        }
        .social-bar a svg {
        width: 12px;
        height: 12px;
        }
        .feedback-float-btn {
        width: 36px;
        height: 36px;
        font-size: 16px;
        bottom: 10px;
        right: 10px;
        }
        }

        html,
        body {
        -ms-overflow-style: none;
        scrollbar-width: none;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
        display: none;
        }

    </style>
</head>
<body>

<?php include 'loading.php'; ?>

<canvas id="star-canvas"></canvas>
<div class="nebula"></div>

<?php include __DIR__ . '/donation_banner.php'; ?>

<div id="main-wrapper" style="display: none;">

    <div class="app-container">
        <div class="login-blob b1"></div>
        <div class="login-blob b2"></div>
        
        <div id="app" class="glass-card">
            
            <!-- VIEW: SETUP -->
            <div id="view-setup" style="display: none;">
                <div class="view-label">Inbox Protocol</div>
                <div class="view-title">CREATE YOUR BOX</div>
                <div class="view-desc">Generate a unique private link. Receive messages from anyone — no signup, no identity.</div>
                <button class="btn btn-primary" id="btn-create-link">CREATE SECURE LINK</button>
                <button class="btn btn-outline" id="btn-restore-view">RESTORE BACKUP</button>
            </div>

            <!-- VIEW: DASHBOARD -->
            <div id="view-dashboard" style="display: none;">
                <div class="view-label">End-to-End Encrypted</div>
                <div class="view-title">SHARE & COLLECT</div>
                <div class="view-desc">Share this secret link. Anyone can send you encrypted messages.</div>
                <div class="url-display" id="url-display">
                    <input type="text" id="shareUrlInput" readonly>
                    <span class="copy-badge">COPY</span>
                </div>
                <button class="btn btn-primary" id="btn-check-inbox">CHECK INBOX</button>
                <button class="insta-btn" id="btn-instagram">Share on Instagram</button>
                <button class="btn btn-outline" id="btn-show-recovery">COPY RECOVERY CODE</button>
                <button class="btn btn-outline" id="btn-auto-delete-settings">⏱ AUTO-DELETE SETTINGS</button>
                <button class="btn btn-outline" id="btn-forget-tab">🧹 FORGET THIS TAB</button>
                <div class="divider"></div>
                <button class="btn btn-danger" id="btn-delete-link">TERMINATE LINK</button>
            </div>

            <div id="view-send" style="display: none;">
                <div class="view-label">Anonymous Whisper</div>
                <div class="view-title">SEND A SECRET</div>
                <div id="sendTargetChip"></div>

                <div class="message-box-wrap">
                    <textarea id="messageToSend" placeholder="Write your anonymous message here... No one will know it's you." rows="5" autocomplete="off"></textarea>
                    <button type="button" class="prompt-random-btn" id="promptRandomBtn" title="Random question">🔀</button>
                </div>
                <div class="cf-turnstile" id="turnstileSendWidget" data-theme="dark" style="margin-bottom: 18px;"></div>
                <button class="btn btn-primary" id="btn-send-secret">ENCRYPT & SEND</button>
                <button class="btn btn-outline" id="btn-cancel-send">CANCEL</button>
            </div>

            <div id="view-send-invalid" style="display: none;">
                <div class="view-label">Anonymous Whisper</div>
                <div class="view-title">LINK NOT AVAILABLE</div>
                <div class="view-desc">This link has expired, been deleted, or never existed. No message can be sent here.</div>
                <button class="btn btn-primary" id="btn-invalid-link-home">GO TO HOMEPAGE</button>
            </div>

            <div id="view-inbox" style="display: none;">
                <div class="inbox-header">
                    <div>
                        <div class="view-label" id="inboxCountLabel">Inbox</div>
                        <div class="view-title">MESSAGES</div>
                    </div>
                    <div class="inbox-header-actions">
                        <button class="btn btn-outline btn-sm" id="btn-refresh-inbox" title="Refresh">↻</button>
                        <button class="btn btn-outline btn-sm" id="btn-back-dashboard">← BACK</button>
                    </div>
                </div>
                <div id="messagesContainer">
                </div>
            </div>

            <div id="view-restore" style="display: none;">
                <div class="view-label">Restore Vault</div>
                <div class="view-title">RECOVERY</div>
                <div class="view-desc">Paste your backup recovery string below to decrypt your box.</div>
                <textarea id="restoreCodeInput" placeholder="eyJp..." rows="4"></textarea>
                <button class="btn btn-primary" id="btn-perform-restore">RESTORE BOX</button>
                <button class="btn btn-outline" id="btn-cancel-restore">BACK</button>
            </div>

        </div>
    </div>
</div>


<?php include __DIR__ . '/footer.php'; ?>

<div id="toast" class="toast"></div>

<div class="modal-overlay" id="secureReminderOverlay" aria-live="polite">
    <div class="modal-box secure-reminder">
        <div class="attention">ATTENTION</div>
        <div class="modal-title">Keep Your Access Safe</div>
        <div class="modal-desc">Your secure link has been created. Please save the information below before this reminder closes.</div>
        <ul class="secure-reminder-list">
            <li><strong>Save your password</strong><br>Keep your Secret Code private and do not share it.</li>
            <li><strong>Save your recovery code</strong><br>Use the recovery option on your dashboard and store the code somewhere safe.</li>
        </ul>
        <div class="secure-reminder-count">Closing automatically in <span id="secureReminderSeconds">15</span>s</div>
        <div class="secure-reminder-progress"><span id="secureReminderBar"></span></div>
    </div>
</div>


<div class="modal-overlay" id="pwModalOverlay">
    <div class="modal-box">
        <div class="modal-title" id="pwModalTitle">Enter Secret Code</div>
        <div class="modal-desc" id="pwModalDesc"></div>
        <form id="pwModalForm" autocomplete="off">
            <div class="modal-input-wrap">
                <input type="password" id="pwModalInput" placeholder="Secret code" autocomplete="off">
                <button type="button" class="modal-toggle-visibility" id="pwModalToggle" aria-label="Toggle password visibility">
                    <svg viewBox="0 0 24 24" id="eyeIcon">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>
            <label class="pw-remember-wrap" id="pwModalRememberWrap">
                <input type="checkbox" id="pwModalRemember">
                <span>Remember on this device</span>
            </label>
            <div class="modal-desc pw-remember-note" id="pwModalRememberNote" style="display:none; margin-top:6px; font-size:0.85em; opacity:0.8;">
                "Remember" only lasts as long as this tab stays open — closing it clears local access. Please save your recovery code so you can always get back in.
            </div>
        </form>
        <div class="modal-error" id="pwModalError"></div>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" id="pwModalCancel">CANCEL</button>
            <button type="button" class="btn btn-primary" id="pwModalConfirm">CONFIRM</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="recoveryModalOverlay">
    <div class="modal-box">
        <div class="modal-title">Recovery Code</div>
        <div class="modal-desc">Copy this code and store it somewhere safe. You'll need it to restore your box on any device.</div>
        <div class="recovery-warning"><span class="icon"></span> <strong>Without this recovery code, you cannot decrypt your secret messages.</strong><br>This is the <strong>only</strong> way to recover your inbox if you lose access. Keep it private and secure.</div>
        <div class="recovery-code-box" id="recoveryCodeDisplay">Loading...</div>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" id="recoveryModalClose">CLOSE</button>
            <button type="button" class="btn btn-primary" id="recoveryModalCopy">COPY CODE</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="forgetTabModalOverlay">
    <div class="modal-box">
        <div class="modal-title">Forget This Tab?</div>
        <div class="modal-desc">This erases the saved password, keys and cookies for this box from this browser only. The box and its messages are <strong>not</strong> deleted from the server.</div>
        <div class="recovery-warning"><span class="icon"></span> <strong>You will only be able to get back into this box using your recovery code.</strong><br>Make sure you've copied it before continuing.</div>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" id="forgetTabModalCancel">CANCEL</button>
            <button type="button" class="btn btn-danger" id="forgetTabModalConfirm">FORGET THIS TAB</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="autoDeleteModalOverlay">
    <div class="modal-box modal-box-wide">
        <div class="modal-title">Auto-Delete Settings</div>
        <div class="modal-desc">Control how long messages and your box stay alive. Saving requires your secret code.</div>

        <div class="modal-scroll-body">
        <div class="tmr-section">
            <div class="tmr-section-label">Self-Destruct Timer <span class="tmr-section-hint">applies to new messages</span></div>
            <div class="tmr-radio-group" id="messageTtlGroup" data-group="messageTtl">
                <label class="tmr-radio-row"><input type="radio" name="messageTtl" value="0" checked><span class="tmr-radio-text">Off</span><span class="tmr-radio-dot"></span></label>
                <label class="tmr-radio-row"><input type="radio" name="messageTtl" value="86400"><span class="tmr-radio-text">After 1 day</span><span class="tmr-radio-dot"></span></label>
                <label class="tmr-radio-row"><input type="radio" name="messageTtl" value="604800"><span class="tmr-radio-text">After 1 week</span><span class="tmr-radio-dot"></span></label>
                <label class="tmr-radio-row"><input type="radio" name="messageTtl" value="2592000"><span class="tmr-radio-text">After 1 month</span><span class="tmr-radio-dot"></span></label>
                <label class="tmr-radio-row"><input type="radio" name="messageTtl" value="31536000"><span class="tmr-radio-text">After 12 months</span><span class="tmr-radio-dot"></span></label>
            </div>
        </div>

        <div class="tmr-section">
            <div class="tmr-account-card" id="accountTtlCard">
                <div class="tmr-account-title">Delete My Box</div>
                <div class="tmr-account-row" id="accountTtlSummaryRow">
                    <span class="tmr-account-row-label">If away for</span>
                    <span class="tmr-account-row-value">
                        <span id="accountTtlValueDisplay">Off</span>
                        <span class="tmr-account-chevron">›</span>
                    </span>
                </div>
                <div class="tmr-radio-group tmr-account-options" id="accountTtlGroup" data-group="accountTtl">
                    <label class="tmr-radio-row"><input type="radio" name="accountTtl" value="0" checked><span class="tmr-radio-text">Off</span><span class="tmr-radio-dot"></span></label>
                    <label class="tmr-radio-row"><input type="radio" name="accountTtl" value="86400"><span class="tmr-radio-text">After 1 day</span><span class="tmr-radio-dot"></span></label>
                    <label class="tmr-radio-row"><input type="radio" name="accountTtl" value="604800"><span class="tmr-radio-text">After 1 week</span><span class="tmr-radio-dot"></span></label>
                    <label class="tmr-radio-row"><input type="radio" name="accountTtl" value="2592000"><span class="tmr-radio-text">After 1 month</span><span class="tmr-radio-dot"></span></label>
                    <label class="tmr-radio-row"><input type="radio" name="accountTtl" value="31536000"><span class="tmr-radio-text">After 12 months</span><span class="tmr-radio-dot"></span></label>
                </div>
            </div>
            <div class="tmr-section-note">If you don't come online at least once within this period, your box and all its messages are permanently deleted. Checking your inbox resets the timer.</div>
        </div>
        </div>

        <div class="modal-sticky-footer">
        <div class="modal-error" id="autoDeleteModalError"></div>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" id="autoDeleteModalCancel">CANCEL</button>
            <button type="button" class="btn btn-primary" id="autoDeleteModalSave">SAVE SETTINGS</button>
        </div>
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
        <a href="https://www.tiktok.com/@secretgate.site" target="_blank" rel="noopener noreferrer" class="tiktok" aria-label="TikTok" tabindex="-1">
            <!-- Add your social media links here -->
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.62v4.08c-1.28.12-2.58-.18-3.68-.71v8.52c0 2.06-.74 4.11-2.24 5.61-1.5 1.49-3.55 2.34-5.66 2.29-2.11-.05-4.12-1-5.55-2.57-1.43-1.57-2.2-3.66-2.1-5.77.1-2.1 1.06-4.11 2.64-5.55 1.58-1.44 3.68-2.16 5.78-1.99v4.1c-.94-.13-1.92.1-2.66.7-.74.6-1.19 1.52-1.2 2.47-.01.95.43 1.87 1.16 2.48.73.61 1.7.85 2.65.73.96-.13 1.82-.6 2.42-1.34.6-.74.88-1.72.75-2.67V.02z"/></svg>
        </a>
    </div>

    <button type="button" class="follow-us-btn" id="followUsBtn" aria-haspopup="true" aria-expanded="false" aria-controls="socialBar" aria-label="Follow us">
        <svg viewBox="0 0 24 24" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    </button>
</div>


<a class="feedback-float-btn" href="feedback.php" aria-label="Send feedback"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg></a>

<script nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">

// DOM XSS – stop unsafe HTML/script injection before it reaches sinks
if (window.trustedTypes && trustedTypes.createPolicy) {
    trustedTypes.createPolicy('default', {
        createHTML: (string) => string,
        createScript: (string) => string,
        createScriptURL: (string) => string
    });
}


// CSRF – keep state-changing actions tied to the same session
let globalCsrfToken = '';
const API_BASE = 'api.php';


// BOT PROTECTION – verify before anonymous writes
const TURNSTILE_SITE_KEY = "<?php echo htmlspecialchars(getenv('CF_TURNSTILE_SITE_KEY') ?: '', ENT_QUOTES, 'UTF-8'); ?>";
let turnstileWidgetId = null;


// INSPIRATION PROMPTS – lower friction to encourage quick responses
const INSPIRATION_PROMPTS = [
    "What's one thing you've always wanted to tell me but never did?",
    "Rate our friendship out of 10, honestly.",
    "What's a secret you've never told anyone?",
    "If you could change one thing about me, what would it be?",
    "What's your honest first impression of me?",
    "Tell me something you like about me that you'd never say to my face.",
    "What's a rumor you've heard about me?",
    "Do you have a crush on someone I know? ",
    "What's the nicest thing you can say about me right now?",
    "Roast me. Be honest.",
    "What's something you think I don't know about myself?",
    "If we could hang out right now, what would we do?",
    "What's a compliment you've been meaning to give me?",
    "What's one question you're too shy to ask me directly?",
    "Send me a random confession — mine or about you, your choice.",
    "What's the weirdest thing you know about me?",
    "Tell me your honest opinion of my last post.",
    "What's something that made you think of me recently?"
];

let lastPromptIndex = -1;

function showRandomPrompt() {
    const box = document.getElementById('messageToSend');
    if (!box) return;
    let idx;
    if (INSPIRATION_PROMPTS.length > 1) {
        do {
            idx = Math.floor(Math.random() * INSPIRATION_PROMPTS.length);
        } while (idx === lastPromptIndex);
    } else {
        idx = 0;
    }
    lastPromptIndex = idx;
    box.value = INSPIRATION_PROMPTS[idx];
    box.focus();
}

function resetPromptChips() { lastPromptIndex = -1; }

// BOT PROTECTION – verify before accepting outbound secrets
function renderTurnstileWidget(attemptsLeft = 20) {
    if (turnstileWidgetId !== null) return;
    const container = document.getElementById('turnstileSendWidget');
    if (!container || !TURNSTILE_SITE_KEY) return;
    if (typeof turnstile === 'undefined') {
        if (attemptsLeft <= 0) {
            console.error('Turnstile script failed to load in time.');
            return;
        }
        setTimeout(() => renderTurnstileWidget(attemptsLeft - 1), 250);
        return;
    }
    turnstileWidgetId = turnstile.render(container, {
        sitekey: TURNSTILE_SITE_KEY,
        theme: 'dark'
    });
}

// CSRF – prepare token before main actions
async function initSecurity() {
    try {
        const resp = await fetch(`${API_BASE}?action=get_csrf`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await resp.json();
        globalCsrfToken = data.csrf_token;
    } catch(e) { console.error("Security init failed", e); }
}

// BACKGROUND – visual effect without external assets
const starCanvas = document.getElementById('star-canvas');
const starCtx = starCanvas.getContext('2d');
let starsArray = [];
function resizeStars() { starCanvas.width = window.innerWidth; starCanvas.height = window.innerHeight; initStars(); }
function initStars() {
    starsArray = []; const count = window.innerWidth < 700 ? 160 : 350;
    for(let i=0;i<count;i++) starsArray.push({ x: Math.random() * starCanvas.width, y: Math.random() * starCanvas.height, size: Math.random() * 1.4, opacity: Math.random(), speed: Math.random() * 0.018 });
}
function drawStars() {
    starCtx.clearRect(0,0,starCanvas.width,starCanvas.height);
    starsArray.forEach(s => { starCtx.fillStyle = `rgba(255,255,255,${Math.abs(Math.sin(s.opacity))*0.7})`; starCtx.beginPath(); starCtx.arc(s.x,s.y,s.size,0,Math.PI*2); starCtx.fill(); s.opacity += s.speed; });
    requestAnimationFrame(drawStars);
}
window.addEventListener('resize', resizeStars); resizeStars(); drawStars();


// TIMESTAMPS – keep them readable without extra server calls
function formatRelativeTime(dateStr) {
    if (!dateStr) return '';
    const normalized = dateStr.includes('T') ? dateStr : dateStr.replace(' ', 'T') + 'Z';
    const then = new Date(normalized);
    if (isNaN(then.getTime())) return dateStr;
    const now = new Date();
    const diffSec = Math.round((now - then) / 1000);
    if (diffSec < 10) return 'just now';
    if (diffSec < 60) return `${diffSec}s ago`;
    const diffMin = Math.round(diffSec / 60);
    if (diffMin < 60) return `${diffMin}m ago`;
    const diffHr = Math.round(diffMin / 60);
    if (diffHr < 24) return `${diffHr}h ago`;
    const diffDay = Math.round(diffHr / 24);
    if (diffDay < 7) return `${diffDay}d ago`;
    return formatFullDate(then);
}

function formatFullDate(dateOrStr) {
    const d = (dateOrStr instanceof Date) ? dateOrStr : new Date((dateOrStr.includes('T') ? dateOrStr : dateOrStr.replace(' ', 'T') + 'Z'));
    if (isNaN(d.getTime())) return String(dateOrStr);
    return d.toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
}

// SENSITIVE ACTIONS – reuse secret-code entry instead of many prompts
function openPasswordModal({ title, desc, confirmLabel = 'CONFIRM', minLength = 0, showRemember = false, defaultRemember = false }) {
    return new Promise((resolve) => {
        const overlay = document.getElementById('pwModalOverlay');
        const titleEl = document.getElementById('pwModalTitle');
        const descEl = document.getElementById('pwModalDesc');
        const input = document.getElementById('pwModalInput');
        const errorEl = document.getElementById('pwModalError');
        const confirmBtn = document.getElementById('pwModalConfirm');
        const cancelBtn = document.getElementById('pwModalCancel');
        const toggleBtn = document.getElementById('pwModalToggle');
        const eyeIcon = document.getElementById('eyeIcon');
        const rememberWrap = document.getElementById('pwModalRememberWrap');
        const rememberCheckbox = document.getElementById('pwModalRemember');
        const rememberNote = document.getElementById('pwModalRememberNote');

        titleEl.textContent = title;
        descEl.textContent = desc;
        confirmBtn.textContent = confirmLabel;
        input.value = '';
        input.type = 'password';
        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />';
        errorEl.textContent = '';

        // Remember-me checkbox visibility + default state
        if (showRemember) {
            rememberWrap.style.display = 'flex';
            rememberCheckbox.checked = !!defaultRemember;
            rememberNote.style.display = 'block';
        } else {
            rememberWrap.style.display = 'none';
            rememberCheckbox.checked = false;
            rememberNote.style.display = 'none';
        }

        overlay.style.display = 'flex';
        setTimeout(() => input.focus(), 60);

        function cleanup(result) {
            overlay.style.display = 'none';
            confirmBtn.removeEventListener('click', onConfirm);
            cancelBtn.removeEventListener('click', onCancel);
            overlay.removeEventListener('click', onOverlayClick);
            input.removeEventListener('keydown', onKeydown);
            toggleBtn.removeEventListener('click', onToggle);
            form.removeEventListener('submit', onSubmit);
            resolve(result);
        }

        function onConfirm() {
            const val = input.value;
            if (minLength && val.length < minLength) {
                errorEl.textContent = `Must be at least ${minLength} characters`;
                input.focus();
                return;
            }
            cleanup({
                password: val,
                rememberMe: showRemember && rememberCheckbox.checked
            });
        }

        function onCancel() { cleanup(null); }
        function onOverlayClick(e) { if (e.target === overlay) onCancel(); }
        function onKeydown(e) {
            if (e.key === 'Enter') { e.preventDefault(); onConfirm(); }
            if (e.key === 'Escape') onCancel();
        }
        const form = document.getElementById('pwModalForm');
        function onToggle() {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            eyeIcon.innerHTML = isPassword ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /><line x1="1" y1="1" x2="23" y2="23" />' : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />';
            input.focus();
        }
        function onSubmit(e) { e.preventDefault(); onConfirm(); }

        confirmBtn.addEventListener('click', onConfirm);
        cancelBtn.addEventListener('click', onCancel);
        overlay.addEventListener('click', onOverlayClick);
        input.addEventListener('keydown', onKeydown);
        toggleBtn.addEventListener('click', onToggle);
        form.addEventListener('submit', onSubmit);
    });
}

// UI FEEDBACK – prevent duplicate clicks during async work
function setBtnLoading(btn, loading) {
    if (!btn) return;
    btn.classList.toggle('is-loading', loading);
    btn.disabled = loading;
}

function showToastMsg(msg, dur=2500, type='') {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = 'toast' + (type ? ' ' + type : '');
    toast.classList.add('show');
    clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(()=> toast.classList.remove('show'), dur);
}

function showLoadingOverlay(show) {
    const el = document.getElementById('loadingOverlay');
    if (show) el.classList.add('active');
    else el.classList.remove('active');
}

// LOCAL VAULT – keep private keys off the server.
//
// Storage rule (this is the actual "remember me" contract):
//   - Remembered  -> localStorage:  survives tab close, works from ANY tab/new tab, until explicitly cleared.
//   - Not remembered -> sessionStorage: only valid in the tab it was created in; the browser
//     wipes it automatically the instant that tab is closed. A brand new tab never sees it.
// storageGet() checks sessionStorage first (this tab's own not-remembered box,
// if any), then falls back to localStorage (a remembered box, visible from any
// tab). Checking session first means opening a not-remembered box in one tab
// doesn't get shadowed by a different remembered box sitting in localStorage,
// and it means setting a not-remembered box no longer has to wipe localStorage
// (which used to blow away a previously remembered box under the same key).
function storageGet(key) {
    try {
        const v = sessionStorage.getItem(key);
        if (v !== null) return v;
    } catch (e) {}
    try { return localStorage.getItem(key); } catch (e) { return null; }
}
function storageSet(key, value, remembered) {
    if (remembered) {
        try { localStorage.setItem(key, value); } catch (e) {}
        try { sessionStorage.removeItem(key); } catch (e) {}
    } else {
        try { sessionStorage.setItem(key, value); } catch (e) {}
        // Intentionally NOT clearing localStorage here: a different, already
        // remembered box may be stored under this same key, and opening a
        // not-remembered box in this tab shouldn't wipe that out.
    }
}
function storageRemove(key) {
    try { localStorage.removeItem(key); } catch (e) {}
    try { sessionStorage.removeItem(key); } catch (e) {}
}

let currentLinkId = storageGet('sb_link_id');

const PBKDF2_ITERATIONS = 300000;
function buf2b64(buf) { return btoa(String.fromCharCode(...new Uint8Array(buf))); }
function b642buf(b64) { return Uint8Array.from(atob(b64), c => c.charCodeAt(0)).buffer; }

async function deriveWrappingKey(password, saltBytes) {
    const baseKey = await window.crypto.subtle.importKey("raw", new TextEncoder().encode(password), { name: "PBKDF2" }, false, ["deriveKey"]);
    return window.crypto.subtle.deriveKey({ name: "PBKDF2", salt: saltBytes, iterations: PBKDF2_ITERATIONS, hash: "SHA-256" }, baseKey, { name: "AES-GCM", length: 256 }, false, ["encrypt", "decrypt"]);
}

async function wrapPrivateKey(privateKey, password) {
    const pkcs8 = await window.crypto.subtle.exportKey("pkcs8", privateKey);
    const salt = window.crypto.getRandomValues(new Uint8Array(16));
    const iv = window.crypto.getRandomValues(new Uint8Array(12));
    const wrappingKey = await deriveWrappingKey(password, salt);
    const encrypted = await window.crypto.subtle.encrypt({ name: "AES-GCM", iv }, wrappingKey, pkcs8);
    return { v: 1, wrapped: buf2b64(encrypted), salt: buf2b64(salt), iv: buf2b64(iv) };
}

async function unwrapPrivateKey(wrappedObj, password) {
    const salt = new Uint8Array(b642buf(wrappedObj.salt));
    const iv = new Uint8Array(b642buf(wrappedObj.iv));
    const wrappingKey = await deriveWrappingKey(password, salt);
    const pkcs8 = await window.crypto.subtle.decrypt({ name: "AES-GCM", iv }, wrappingKey, b642buf(wrappedObj.wrapped));
    return window.crypto.subtle.importKey("pkcs8", pkcs8, { name: "RSA-OAEP", hash: "SHA-256" }, true, ["decrypt"]);
}

// =====================================================
// SERVER AUTH KEY – Derives a deterministic auth_key from the password without sending the password to the server.
// =====================================================
async function deriveAuthKey(password, existingSaltB64 = null) {
    const salt = existingSaltB64
        ? new Uint8Array(b642buf(existingSaltB64))
        : window.crypto.getRandomValues(new Uint8Array(16));

    const baseKey = await window.crypto.subtle.importKey(
        "raw",
        new TextEncoder().encode(password),
        { name: "PBKDF2" },
        false,
        ["deriveBits"]
    );

    const bits = await window.crypto.subtle.deriveBits(
        { name: "PBKDF2", salt, iterations: PBKDF2_ITERATIONS, hash: "SHA-256" },
        baseKey,
        256
    );

    const authKey = [...new Uint8Array(bits)]
        .map(b => b.toString(16).padStart(2, '0'))
        .join('');

    const authSalt = buf2b64(salt.buffer);

    return { authKey, authSalt };
}

// Fetch auth_salt from server for existing links
async function fetchAuthSalt(publicId) {
    try {
        const res = await fetch(`${API_BASE}?action=get_auth_salt&public_id=${encodeURIComponent(publicId)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        return data.success ? data.auth_salt : null;
    } catch (e) {
        return null;
    }
}

async function storeWrappedPrivateKey(id, wrappedObj, remembered) {
    storageSet(`sb_${id}`, JSON.stringify(wrappedObj), remembered);
}
async function getWrappedPrivateKey(id) {
    const raw = storageGet(`sb_${id}`);
    try { return raw ? JSON.parse(raw) : undefined; } catch (e) { return undefined; }
}

// =====================================================
// DEVICE KEY – a random, non-extractable AES-GCM key kept in IndexedDB.
// Used only to encrypt the "remembered" secret code before it's written to
// localStorage. Because the key is generated with extractable:false, no
// script (including an XSS payload doing a raw storage/IndexedDB dump, a
// browser extension reading disk files, or someone copying the profile off
// a stolen device) can ever read out its bytes — it can only be *used* via
// crypto.subtle.encrypt/decrypt while pointed at by a live page. That closes
// the previous hole where the plaintext password sat in localStorage right
// next to the wrapped private key it was meant to protect.
// =====================================================
const SB_KEYSTORE_DB = 'sb_keystore';
const SB_KEYSTORE_STORE = 'keys';
const SB_DEVICE_KEY_ID = 'device_key';
const SB_PWD_ENC_PREFIX = 'SBENC1:';

function sbOpenKeystore() {
    return new Promise((resolve, reject) => {
        try {
            const req = indexedDB.open(SB_KEYSTORE_DB, 1);
            req.onupgradeneeded = () => { req.result.createObjectStore(SB_KEYSTORE_STORE); };
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        } catch (e) { reject(e); }
    });
}

async function sbIdbGet(key) {
    try {
        const db = await sbOpenKeystore();
        return await new Promise((resolve, reject) => {
            const tx = db.transaction(SB_KEYSTORE_STORE, 'readonly');
            const req = tx.objectStore(SB_KEYSTORE_STORE).get(key);
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    } catch (e) { return undefined; }
}

async function sbIdbSet(key, value) {
    try {
        const db = await sbOpenKeystore();
        await new Promise((resolve, reject) => {
            const tx = db.transaction(SB_KEYSTORE_STORE, 'readwrite');
            tx.objectStore(SB_KEYSTORE_STORE).put(value, key);
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
        });
    } catch (e) {}
}

async function getOrCreateDeviceKey() {
    let key = await sbIdbGet(SB_DEVICE_KEY_ID);
    if (key) return key;
    key = await window.crypto.subtle.generateKey(
        { name: 'AES-GCM', length: 256 },
        false, // non-extractable — raw bytes can never be read out by any script
        ['encrypt', 'decrypt']
    );
    await sbIdbSet(SB_DEVICE_KEY_ID, key);
    return key;
}

async function storeDevicePassword(id, pwd, remembered) {
    if (!remembered) {
        // Not-remembered boxes already live in sessionStorage only, wiped the
        // instant this tab closes — nothing persists to disk, so plaintext
        // here is fine and matches the existing storage contract above.
        storageSet(`sb_pwd_${id}`, pwd, false);
        return;
    }

    // Remembered boxes: never persist the plaintext password. Encrypt it
    // with the non-extractable per-device key before it touches localStorage.
    try {
        const deviceKey = await getOrCreateDeviceKey();
        const iv = window.crypto.getRandomValues(new Uint8Array(12));
        const ciphertext = await window.crypto.subtle.encrypt(
            { name: 'AES-GCM', iv }, deviceKey, new TextEncoder().encode(pwd)
        );
        const payload = SB_PWD_ENC_PREFIX + JSON.stringify({ c: buf2b64(ciphertext), iv: buf2b64(iv) });
        storageSet(`sb_pwd_${id}`, payload, true);
    } catch (e) {
        // WebCrypto/IndexedDB unavailable — fail closed rather than fall back
        // to plaintext. User will just be asked for the code again next time.
        storageRemove(`sb_pwd_${id}`);
    }
}

async function getDevicePassword(id) {
    const raw = storageGet(`sb_pwd_${id}`);
    if (!raw) return null;

    // Legacy plaintext entries saved before this fix: use it once to unlock,
    // then immediately re-save it encrypted so plaintext doesn't linger.
    if (!raw.startsWith(SB_PWD_ENC_PREFIX)) {
        const wasRemembered = !!(function () { try { return localStorage.getItem(`sb_pwd_${id}`); } catch (e) { return null; } })();
        try { await storeDevicePassword(id, raw, wasRemembered); } catch (e) {}
        return raw;
    }

    try {
        const obj = JSON.parse(raw.slice(SB_PWD_ENC_PREFIX.length));
        const deviceKey = await getOrCreateDeviceKey();
        const plain = await window.crypto.subtle.decrypt(
            { name: 'AES-GCM', iv: new Uint8Array(b642buf(obj.iv)) },
            deviceKey, b642buf(obj.c)
        );
        return new TextDecoder().decode(plain);
    } catch (e) {
        // Device key missing/rotated or data corrupted — can't recover the
        // password; drop the stale entry so the user is cleanly re-prompted.
        storageRemove(`sb_pwd_${id}`);
        return null;
    }
}

async function deleteDevicePassword(id) {
    storageRemove(`sb_pwd_${id}`);
}

let sessionPrivateKey = null;
let sessionPrivateKeyId = null;
let sessionPassword = null;

// UNLOCK – avoid asking secret code repeatedly in one session
async function unlockPrivateKey({ title = 'Unlock Your Box', desc = 'Enter your Secret Code to decrypt.' } = {}) {
    if (sessionPrivateKey && sessionPrivateKeyId === currentLinkId) return sessionPrivateKey;
    const wrappedObj = await getWrappedPrivateKey(`priv_${currentLinkId}`);
    if (!wrappedObj) { showToastMsg('No private key on this device'); return null; }

    const storedPwd = await getDevicePassword(currentLinkId);
    if (storedPwd) {
        try {
            const privKey = await unwrapPrivateKey(wrappedObj, storedPwd);
            sessionPrivateKey = privKey; sessionPrivateKeyId = currentLinkId; sessionPassword = storedPwd;
            return privKey;
        } catch (e) {}
    }

    // LOCAL BACKOFF – not real protection (a wiped storage resets it), but it
    // slows down casual guessing directly against the client-side unwrap.
    const failKey = `sb_fail_${currentLinkId}`;
    const failedAttempts = parseInt(localStorage.getItem(failKey) || '0', 10);
    if (failedAttempts >= 5) {
        const delayMs = Math.min(30000, Math.pow(2, failedAttempts - 5) * 1000);
        showToastMsg(`Too many attempts. Please wait ${Math.round(delayMs / 1000)}s and try again.`, 3000, 'error');
        await new Promise((r) => setTimeout(r, delayMs));
    }

    const result = await openPasswordModal({
        title, desc,
        confirmLabel: 'UNLOCK',
        showRemember: true,
        defaultRemember: false
    });
    if (!result) return null;
    const { password: pwd, rememberMe } = result;
    try {
        const privKey = await unwrapPrivateKey(wrappedObj, pwd);
        sessionPrivateKey = privKey; sessionPrivateKeyId = currentLinkId; sessionPassword = pwd;
        try { localStorage.removeItem(failKey); } catch (e) {}

        if (rememberMe) {
            await storeDevicePassword(currentLinkId, pwd, true);
        } else {
            await storeDevicePassword(currentLinkId, pwd, false);
        }
        return privKey;

    } catch (e) {
        try { localStorage.setItem(failKey, String(failedAttempts + 1)); } catch (e2) {}
        showToastMsg('Incorrect secret code!');
        return null;
    }
}

// LINK ID – avoid guessable IDs
function generateSecureId() {
    const buf = new Uint8Array(8);
    window.crypto.getRandomValues(buf);
    return Array.from(buf).map(b => b.toString(16).padStart(2, '0')).join('');
}

// RECOVERY REMINDER – make creator save recovery before leaving
let secureReminderTimer = null;
const SECURE_REMINDER_DURATION = 15;
function closeSecureReminder() {
    const overlay = document.getElementById('secureReminderOverlay');
    clearInterval(secureReminderTimer);
    if (overlay) overlay.style.display = 'none';
}
function showSecureReminder() {
    const overlay = document.getElementById('secureReminderOverlay');
    const secondsEl = document.getElementById('secureReminderSeconds');
    const bar = document.getElementById('secureReminderBar');
    if (!overlay || !secondsEl || !bar) return;
    clearInterval(secureReminderTimer);
    let remaining = SECURE_REMINDER_DURATION;
    overlay.style.display = 'flex';
    secondsEl.textContent = remaining;
    bar.style.transition = 'none';
    bar.style.transform = 'scaleX(1)';
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            bar.style.transition = `transform ${SECURE_REMINDER_DURATION}s linear`;
            bar.style.transform = 'scaleX(0)';
        });
    });
    secureReminderTimer = setInterval(() => {
        remaining -= 1;
        secondsEl.textContent = Math.max(remaining, 0);
        if (remaining <= 0) {
            clearInterval(secureReminderTimer);
            overlay.style.display = 'none';
        }
    }, 1000);
}

// LINK CREATION – generate keys before server call
async function generateNewLink() {
    if (!window.crypto || !window.crypto.subtle) {
        showToastMsg('Security Error: Web Crypto requires HTTPS or localhost!');
        alert('Web Cryptography API is disabled by your browser because this site is running on insecure HTTP.\n\nPlease access via http://localhost or setup HTTPS!');
        return;
    }

    const result = await openPasswordModal({
        title: 'Secure Your Box',
        desc: 'Create a Secret Code to protect your inbox. Minimum 8 characters — you\'ll need this to restore your box later.',
        confirmLabel: 'CREATE',
        minLength: 8,
        showRemember: true,
        defaultRemember: false
    });
    if (!result) { showToastMsg('Secret code must be at least 8 characters!'); return; }
    const { password: pwd, rememberMe } = result;
    

    const createBtn = document.getElementById('btn-create-link');
    setBtnLoading(createBtn, true);

    try {
    const keyPair = await window.crypto.subtle.generateKey({ name: "RSA-OAEP", modulusLength: 2048, publicExponent: new Uint8Array([1,0,1]), hash: "SHA-256" }, true, ["encrypt", "decrypt"]);

    const publicId = generateSecureId();
    const pubJWK = await window.crypto.subtle.exportKey("jwk", keyPair.publicKey);

    const wrappedPriv = await wrapPrivateKey(keyPair.privateKey, pwd);
    await storeWrappedPrivateKey(`priv_${publicId}`, wrappedPriv, rememberMe);
    storageSet('sb_link_id', publicId, rememberMe);
    currentLinkId = publicId;
    sessionPrivateKey = keyPair.privateKey;
    sessionPrivateKeyId = publicId;
    sessionPassword = pwd;

    await storeDevicePassword(publicId, pwd, rememberMe);

    // Derive auth_key — the password is never sent to the server.
    const { authKey, authSalt } = await deriveAuthKey(pwd);

    const fd = new FormData();
    fd.append('action', 'create_link');
    fd.append('csrf_token', globalCsrfToken);
    fd.append('public_id', publicId);
    fd.append('public_key', JSON.stringify(pubJWK));
    fd.append('auth_key', authKey);
    fd.append('auth_salt', authSalt);

    const res = await fetch(API_BASE, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd }).catch(e => console.warn(e));
    if (!res) { showToastMsg('Something went wrong. Please refresh the page and try again.'); return; }
    const data = await res.json();
    if (data.success) {
        showDashboard();
        showToastMsg('Secure link created!');
        setTimeout(showSecureReminder, 250);
    } else {
        showToastMsg('Error: ' + data.error);
    }
    } finally {
        setBtnLoading(createBtn, false);
    }
}

// RECOVERY EXPORT – allow restoring on another device
async function showRecoveryCode() {
    const privKey = await unlockPrivateKey({ title: 'Unlock to View Recovery Code', desc: 'Enter your Secret Code to export your recovery key.' });
    if(!privKey) return;
    if (!sessionPassword) { showToastMsg('Could not verify secret code'); return; }

    const recoveryBtn = document.getElementById('btn-show-recovery');
    setBtnLoading(recoveryBtn, true);
    try {
    const wrappedForExport = await wrapPrivateKey(privKey, sessionPassword);
    const recoveryData = { id: currentLinkId, wrapped: wrappedForExport };
    const recoveryString = btoa(JSON.stringify(recoveryData));

    const overlay = document.getElementById('recoveryModalOverlay');
    const display = document.getElementById('recoveryCodeDisplay');
    display.textContent = recoveryString;
    overlay.style.display = 'flex';
    } finally {
        setBtnLoading(recoveryBtn, false);
    }
}

document.getElementById('recoveryModalClose').addEventListener('click', function() {
    document.getElementById('recoveryModalOverlay').style.display = 'none';
});
document.getElementById('recoveryModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.getElementById('recoveryModalCopy').addEventListener('click', function() {
    const code = document.getElementById('recoveryCodeDisplay').textContent;
    navigator.clipboard.writeText(code).then(() => {
        showToastMsg('Recovery code copied!', 2000, 'success');
        document.getElementById('recoveryModalOverlay').style.display = 'none';
    }).catch(() => {
        const range = document.createRange();
        const sel = window.getSelection();
        const el = document.getElementById('recoveryCodeDisplay');
        range.selectNodeContents(el);
        sel.removeAllRanges();
        sel.addRange(range);
        document.execCommand('copy');
        sel.removeAllRanges();
        showToastMsg('Recovery code copied!', 2000, 'success');
        document.getElementById('recoveryModalOverlay').style.display = 'none';
    });
});

// RESTORE FLOW – verify secret code before importing key
async function performRestore() {
    const raw = document.getElementById('restoreCodeInput').value.trim();
    if(!raw) { showToastMsg('Paste recovery code first'); return; }
    try {
        let base64String = raw;
        const match = raw.match(/(eyJp[A-Za-z0-9+/=]+)/);
        if(match) base64String = match[1];
        const parsed = JSON.parse(atob(base64String));
        const id = parsed.id;
        const result = await openPasswordModal({
            title: 'Unlock Your Box',
            desc: `Enter the Secret Code for Box [${id}] to restore it on this device.`,
            confirmLabel: 'UNLOCK',
            showRemember: true,
            defaultRemember: false
        });
        if(!result) return;
        const { password: pwd, rememberMe } = result;

        const restoreBtn = document.getElementById('btn-perform-restore');
        setBtnLoading(restoreBtn, true);
        try {
        const saltB64 = await fetchAuthSalt(id);
        if (!saltB64) { showToastMsg('Could not fetch link info'); return; }

        const { authKey } = await deriveAuthKey(pwd, saltB64);

        const fd = new FormData();
        fd.append('action', 'verify_restore');
        fd.append('csrf_token', globalCsrfToken);
        fd.append('public_id', id);
        fd.append('auth_key', authKey);
        const res = await fetch(API_BASE, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd });
        const data = await res.json();
        if (!data.success) { showToastMsg('Incorrect secret code!'); return; }
        // The server rotates the session/CSRF token on a successful restore
        // (session-fixation hardening); pick up the new token for later calls.
        if (data.csrf_token) globalCsrfToken = data.csrf_token;

        let privKey;
        if (parsed.wrapped) {
            try { privKey = await unwrapPrivateKey(parsed.wrapped, pwd); }
            catch (e) { showToastMsg('Secret code does not match this recovery code.'); return; }
        } else if (parsed.key) {
            privKey = await window.crypto.subtle.importKey("jwk", parsed.key, { name: "RSA-OAEP", hash: "SHA-256" }, true, ["decrypt"]);
        } else {
            showToastMsg('Invalid recovery string format');
            return;
        }

        const wrappedPriv = await wrapPrivateKey(privKey, pwd);
        await storeWrappedPrivateKey(`priv_${id}`, wrappedPriv, rememberMe);
        storageSet('sb_link_id', id, rememberMe);
        currentLinkId = id;
        sessionPrivateKey = privKey;
        sessionPrivateKeyId = id;
        sessionPassword = pwd;

        await storeDevicePassword(id, pwd, rememberMe);

        showDashboard();
        showToastMsg('Box restored successfully');
        } finally {
            setBtnLoading(restoreBtn, false);
        }
    } catch(e) { 
        console.error(e);
        showToastMsg('Invalid recovery string format'); 
    }
}

// DECRYPTION – support both RSA-only and hybrid AES messages
async function decryptMessageContent(encBase64, priv) {
    if(!priv) return "Key missing (device only)";
    const binary = Uint8Array.from(atob(encBase64), c=>c.charCodeAt(0));
    const RSA_CIPHERTEXT_LEN = 256;
    const IV_LEN = 12;

    try {
        if (binary.length > RSA_CIPHERTEXT_LEN) {
            const encryptedAesKey = binary.slice(0, RSA_CIPHERTEXT_LEN);
            const iv = binary.slice(RSA_CIPHERTEXT_LEN, RSA_CIPHERTEXT_LEN + IV_LEN);
            const aesCiphertext = binary.slice(RSA_CIPHERTEXT_LEN + IV_LEN);

            const rawAesKey = await window.crypto.subtle.decrypt({ name: "RSA-OAEP" }, priv, encryptedAesKey);
            const aesKey = await window.crypto.subtle.importKey("raw", rawAesKey, { name: "AES-GCM" }, false, ["decrypt"]);
            const dec = await window.crypto.subtle.decrypt({ name: "AES-GCM", iv }, aesKey, aesCiphertext);
            return new TextDecoder().decode(dec);
        } else {
            const dec = await window.crypto.subtle.decrypt({ name: "RSA-OAEP" }, priv, binary);
            return new TextDecoder().decode(dec);
        }
    } catch(e) { return "Decryption failed"; }
}

// INBOX FETCH – decrypt only on the owner's device
async function fetchMessages() {
    showView('view-inbox');
    const container = document.getElementById('messagesContainer');
    const countLabel = document.getElementById('inboxCountLabel');
    container.innerHTML = '';
    if (countLabel) countLabel.textContent = 'Inbox';

    const priv = await unlockPrivateKey({
        title: 'Unlock Your Inbox',
        desc: 'Enter your Secret Code to decrypt your messages.'
    });
    if (!priv) {
        container.innerHTML = '<div class="empty-msg">Inbox locked. Click "Check Inbox" again and enter your Secret Code to view messages.</div>';
        return;
    }
    container.innerHTML = '<div class="loading">Decrypting messages...</div>';
    try {
        const res = await fetch(`${API_BASE}?action=get_messages&public_id=${currentLinkId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        if(!data.messages || data.messages.length === 0) {
            if (countLabel) countLabel.textContent = 'Inbox — 0 messages';
            container.innerHTML = '<div class="empty-msg">No messages yet. Share your link to receive secrets.</div>'; return;
        }
        if (countLabel) countLabel.textContent = `Inbox — ${data.messages.length} message${data.messages.length === 1 ? '' : 's'}`;
        container.innerHTML = '';
        const TRUNCATE_AT = 220;

        for(const msg of data.messages) {
            const plain = await decryptMessageContent(msg.encrypted_content, priv);
            const card = document.createElement('div');
            card.className = 'message-card';

            const textEl = document.createElement('div');
            textEl.className = 'message-text';
            textEl.textContent = plain;

            const isLong = plain.length > TRUNCATE_AT;
            if (isLong) textEl.classList.add('truncated');

            const timeEl = document.createElement('div');
            timeEl.className = 'message-time';
            timeEl.textContent = formatRelativeTime(msg.created_at);
            if (msg.created_at) timeEl.title = formatFullDate(msg.created_at);

            card.appendChild(textEl);

            if (isLong) {
                const toggleBtn = document.createElement('button');
                toggleBtn.className = 'show-more-btn';
                toggleBtn.textContent = 'Show more';
                toggleBtn.addEventListener('click', () => {
                    const collapsed = textEl.classList.toggle('truncated');
                    toggleBtn.textContent = collapsed ? 'Show more' : 'Show less';
                });
                card.appendChild(toggleBtn);
            }

            const actionsEl = document.createElement('div');
            actionsEl.className = 'message-actions';

            const storyBtn = document.createElement('button');
            storyBtn.className = 'btn btn-outline btn-sm';
            storyBtn.textContent = 'Create Story';
            storyBtn.addEventListener('click', () => shareToStory(plain));
            actionsEl.appendChild(storyBtn);

            const dlBtn = document.createElement('button');
            dlBtn.className = 'btn btn-outline btn-sm';
            dlBtn.textContent = 'Download';
            dlBtn.addEventListener('click', () => downloadMessageCard(plain));
            actionsEl.appendChild(dlBtn);

            card.appendChild(timeEl);
            card.appendChild(actionsEl);
            container.appendChild(card);
        }
    } catch(e) {
        console.error(e);
        container.innerHTML = '<div class="empty-msg">Could not load your messages. Please refresh the page and try again.</div>';
    }
}

// ANONYMOUS SEND – encrypt before upload
async function submitSecretMessage() {
    const params = new URLSearchParams(window.location.search);
    const targetId = params.get('send');
    const msgText = document.getElementById('messageToSend').value.trim();
    if(!msgText) { showToastMsg('Message cannot be empty'); return; }

    const turnstileToken = (typeof turnstile !== 'undefined' && turnstileWidgetId !== null) ? turnstile.getResponse(turnstileWidgetId) : '';
    if (!turnstileToken) { showToastMsg('Please complete the verification check'); return; }

    const sendBtn = document.getElementById('btn-send-secret');
    setBtnLoading(sendBtn, true);

    try {
        const pubRes = await fetch(`${API_BASE}?action=get_public_key&public_id=${targetId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const pubData = await pubRes.json();
        if(!pubData.success) { showToastMsg('Link is deleted'); setBtnLoading(sendBtn, false); return; }
        const pubKey = await window.crypto.subtle.importKey("jwk", JSON.parse(pubData.public_key), { name: "RSA-OAEP", hash: "SHA-256" }, true, ["encrypt"]);

        const aesKey = await window.crypto.subtle.generateKey({ name: "AES-GCM", length: 256 }, true, ["encrypt", "decrypt"]);
        const iv = window.crypto.getRandomValues(new Uint8Array(12));
        const aesCiphertextBuf = await window.crypto.subtle.encrypt({ name: "AES-GCM", iv }, aesKey, new TextEncoder().encode(msgText));

        const rawAesKey = await window.crypto.subtle.exportKey("raw", aesKey);
        const encryptedAesKeyBuf = await window.crypto.subtle.encrypt({ name: "RSA-OAEP" }, pubKey, rawAesKey);

        const encryptedAesKey = new Uint8Array(encryptedAesKeyBuf);
        const aesCiphertext = new Uint8Array(aesCiphertextBuf);

        const combined = new Uint8Array(encryptedAesKey.length + iv.length + aesCiphertext.length);
        combined.set(encryptedAesKey, 0);
        combined.set(iv, encryptedAesKey.length);
        combined.set(aesCiphertext, encryptedAesKey.length + iv.length);

        let binaryStr = '';
        for (let i = 0; i < combined.length; i++) binaryStr += String.fromCharCode(combined[i]);
        const encryptedB64 = btoa(binaryStr);

        const fd = new FormData();
        fd.append('action', 'send_message');
        fd.append('csrf_token', globalCsrfToken);
        fd.append('public_id', targetId);
        fd.append('encrypted_message', encryptedB64);
        fd.append('cf_turnstile_response', turnstileToken);
        const sendRes = await fetch(API_BASE, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd });
        const sendData = await sendRes.json();
        if (sendData.success) {
            document.getElementById('messageToSend').value = '';
            resetPromptChips();
            showToastMsg('Secret sent anonymously!');
            setTimeout(()=> { window.location.href = window.location.pathname; }, 1500);
        } else {
            showToastMsg('' + sendData.error);
            if (typeof turnstile !== 'undefined' && turnstileWidgetId !== null) turnstile.reset(turnstileWidgetId);
            setBtnLoading(sendBtn, false);
        }
    } catch(e) {
        console.error('submitSecretMessage failed:', e);
        showToastMsg('Failed to send');
        if (typeof turnstile !== 'undefined' && turnstileWidgetId !== null) turnstile.reset(turnstileWidgetId);
        setBtnLoading(sendBtn, false);
    }
}

// DESTRUCTIVE ACTION – require secret code before delete
async function deletePermanentLink() {
    const result = await openPasswordModal({
        title: 'Terminate Link',
        desc: 'Enter your Secret Code to permanently delete this link and all its messages. This action cannot be undone.',
        confirmLabel: 'DELETE FOREVER',
        showRemember: false
    });
    if (!result) { showToastMsg('Termination cancelled'); return; }
    const { password: pwd } = result;

    const deleteBtn = document.getElementById('btn-delete-link');
    setBtnLoading(deleteBtn, true);
    try {
        const saltB64 = await fetchAuthSalt(currentLinkId);
        if (!saltB64) { showToastMsg('Could not fetch link info'); return; }

        const { authKey } = await deriveAuthKey(pwd, saltB64);

        const fd2 = new FormData();
        fd2.append('action', 'delete_link');
        fd2.append('csrf_token', globalCsrfToken);
        fd2.append('public_id', currentLinkId);
        fd2.append('auth_key', authKey);
        const delRes = await fetch(API_BASE, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd2 });
        const delData = await delRes.json();
        if (!delData.success) { showToastMsg('' + (delData.error || 'Could not delete link.'), 3000, 'error'); return; }

        sessionPrivateKey = null; sessionPrivateKeyId = null; sessionPassword = null;
        clearAllBrowserTraces();
        showToastMsg('Link terminated permanently.', 3000, 'success');
        setTimeout(() => { window.location.reload(); }, 800);
    } catch(e) {
        showToastMsg('Error verifying password', 3000, 'error');
    } finally {
        setBtnLoading(deleteBtn, false);
    }
}

// EXPIRY CONTROL – owner-managed retention
function initAutoDeleteRadioGroup(groupEl, onChange) {
    const rows = groupEl.querySelectorAll('.tmr-radio-row');
    function syncHighlight() {
        rows.forEach(row => {
            const input = row.querySelector('input[type="radio"]');
            row.classList.toggle('is-checked', input.checked);
        });
        if (typeof onChange === 'function') onChange();
    }
    rows.forEach(row => {
        const input = row.querySelector('input[type="radio"]');
        input.addEventListener('change', syncHighlight);
        row.addEventListener('click', (e) => {
            if (e.target.tagName === 'INPUT') return;
            input.checked = true;
            input.dispatchEvent(new Event('change'));
        });
    });
    syncHighlight();
}

function setTtlRadioGroup(groupEl, seconds) {
    const inputs = groupEl.querySelectorAll('input[type="radio"]');
    let matched = false;
    inputs.forEach(input => {
        const isMatch = (!seconds || seconds <= 0) ? input.value === '0' : Number(input.value) === seconds;
        input.checked = isMatch;
        if (isMatch) matched = true;
    });
    if (!matched) groupEl.querySelector('input[value="0"]').checked = true;

    groupEl.querySelectorAll('.tmr-radio-row').forEach(row => {
        const input = row.querySelector('input[type="radio"]');
        row.classList.toggle('is-checked', input.checked);
    });
}

function getTtlRadioGroupValue(groupEl) {
    const checked = groupEl.querySelector('input[type="radio"]:checked');
    return checked ? Number(checked.value) : 0;
}

function updateAccountTtlSummary() {
    const groupEl = document.getElementById('accountTtlGroup');
    const display = document.getElementById('accountTtlValueDisplay');
    if (!groupEl || !display) return;
    const checked = groupEl.querySelector('input[type="radio"]:checked');
    if (!checked || checked.value === '0') { display.textContent = 'Off'; return; }
    const row = checked.closest('.tmr-radio-row');
    display.textContent = row ? row.querySelector('.tmr-radio-text').textContent.trim() : 'Off';
}

let autoDeleteInitialized = false;
function ensureAutoDeleteRadiosInit() {
    if (autoDeleteInitialized) return;
    initAutoDeleteRadioGroup(document.getElementById('messageTtlGroup'));
    initAutoDeleteRadioGroup(document.getElementById('accountTtlGroup'), updateAccountTtlSummary);

    const accountCard = document.getElementById('accountTtlCard');
    const summaryRow = document.getElementById('accountTtlSummaryRow');
    if (accountCard && summaryRow) {
        summaryRow.addEventListener('click', () => accountCard.classList.toggle('expanded'));
    }

    updateAccountTtlSummary();
    autoDeleteInitialized = true;
}

async function openAutoDeleteModal() {
    ensureAutoDeleteRadiosInit();
    const overlay = document.getElementById('autoDeleteModalOverlay');
    const errorEl = document.getElementById('autoDeleteModalError');
    const saveBtn = document.getElementById('autoDeleteModalSave');
    const accountCard = document.getElementById('accountTtlCard');
    if (accountCard) accountCard.classList.remove('expanded');
    errorEl.textContent = '';
    overlay.style.display = 'flex';

    try {
        const res = await fetch(`${API_BASE}?action=get_auto_delete_settings&public_id=${encodeURIComponent(currentLinkId)}`, {
            method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            setTtlRadioGroup(document.getElementById('messageTtlGroup'), data.message_ttl_seconds || 0);
            setTtlRadioGroup(document.getElementById('accountTtlGroup'), data.account_ttl_seconds || 0);
            updateAccountTtlSummary();
        } else {
            errorEl.textContent = data.error || 'Could not load current settings, please refresh the page.';
        }
    } catch (e) {
        errorEl.textContent = 'Could not load current settings, please refresh the page.';
    }

    function close() {
        overlay.style.display = 'none';
        overlay.removeEventListener('click', onOverlayClick);
        cancelBtn.removeEventListener('click', onCancel);
    }
    function onOverlayClick(e) { if (e.target === overlay) close(); }
    function onCancel() { close(); }

    const cancelBtn = document.getElementById('autoDeleteModalCancel');
    overlay.addEventListener('click', onOverlayClick);
    cancelBtn.addEventListener('click', onCancel);

    saveBtn.onclick = async function () {
        errorEl.textContent = '';
        const messageTtl = getTtlRadioGroupValue(document.getElementById('messageTtlGroup'));
        const accountTtl = getTtlRadioGroupValue(document.getElementById('accountTtlGroup'));

        const result = await openPasswordModal({
            title: '⏱ Confirm Auto-Delete Settings',
            desc: 'Enter your Secret Code to save these auto-delete settings.',
            confirmLabel: 'SAVE',
            showRemember: false
        });
        if (!result) return;
        const { password: pwd } = result;

        setBtnLoading(saveBtn, true);
        try {
            const saltB64 = await fetchAuthSalt(currentLinkId);
            if (!saltB64) { errorEl.textContent = 'Could not fetch link info'; return; }

            const { authKey } = await deriveAuthKey(pwd, saltB64);

            const fd = new FormData();
            fd.append('action', 'set_auto_delete_settings');
            fd.append('csrf_token', globalCsrfToken);
            fd.append('public_id', currentLinkId);
            fd.append('auth_key', authKey);
            fd.append('message_ttl_seconds', String(messageTtl));
            fd.append('account_ttl_seconds', String(accountTtl));
            const res = await fetch(API_BASE, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd });
            const data = await res.json();
            if (!data.success) {
                errorEl.textContent = data.error || 'Could not save settings.';
                return;
            }
            showToastMsg('Auto-delete settings saved.', 2500, 'success');
            const box = overlay.querySelector('.modal-box');
            box.classList.remove('tmr-saved-flash');
            void box.offsetWidth;
            box.classList.add('tmr-saved-flash');
            setTimeout(close, 500);
        } catch (e) {
            errorEl.textContent = 'Something went wrong. Please refresh the page and try again.';
        } finally {
            setBtnLoading(saveBtn, false);
        }
    };
}

// ROUTING – URL decides send vs dashboard vs setup
async function initAppView() {
    const params = new URLSearchParams(window.location.search);
    const sendTo = params.get('send');
    if(sendTo) {
        await routeToSendView(sendTo);
    } else if(currentLinkId) { showDashboard(); } else { showView('view-setup'); }
}

// LINK VALIDATION – confirm the target link still exists before letting anyone type a message
async function routeToSendView(targetId) {
    showView('view-send-invalid');
    try {
        const res = await fetch(`${API_BASE}?action=get_public_key&public_id=${encodeURIComponent(targetId)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        if (data.success) {
            showView('view-send');
            document.getElementById('sendTargetChip').innerHTML = `<div class="send-to-chip">CONNECTION SECURE</div>`;
        } else {
            showView('view-send-invalid');
        }
    } catch (e) {
        showView('view-send-invalid');
    }
}

function showView(viewId) {
    document.querySelectorAll('#app > div').forEach(div => {
        if(div.id && div.id.startsWith('view-')) div.style.display = 'none';
    });
    const target = document.getElementById(viewId);
    if (target) target.style.display = 'flex';
}

function showDashboard() {
    showView('view-dashboard');
    document.getElementById('shareUrlInput').value = `${window.location.origin}${window.location.pathname}?send=${currentLinkId}`;
    verifyLinkStillExists();
}

// DEAD LINK – clean local state if server no longer knows it
async function verifyLinkStillExists() {
    if (!currentLinkId) return;
    const checkedId = currentLinkId;
    try {
        const res = await fetch(`${API_BASE}?action=get_public_key&public_id=${checkedId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        if (data.success) return;
    } catch (e) { return; }
    if (currentLinkId !== checkedId) return;
    await clearLocalLinkData(checkedId);
    showToastMsg('This link no longer exists — it may have been deleted.', 4000, 'error');
    showView('view-setup');
}

async function clearLocalLinkData(linkId) {
    storageRemove('sb_link_id');
    storageRemove(`sb_priv_${linkId}`);
    storageRemove(`sb_pwd_${linkId}`);
    storageRemove(`sb_fail_${linkId}`);
    sessionPrivateKey = null; sessionPrivateKeyId = null; sessionPassword = null;
    currentLinkId = null;
}

// FULL WIPE – on account deletion, scrub everything this browser stored for THIS
// app (keys prefixed "sb_"), without touching unrelated data other apps on the
// same domain may have placed in localStorage/sessionStorage.
function clearAllBrowserTraces() {
    try {
        Object.keys(localStorage)
            .filter((k) => k.startsWith('sb_'))
            .forEach((k) => localStorage.removeItem(k));
    } catch (e) {}
    try {
        Object.keys(sessionStorage)
            .filter((k) => k.startsWith('sb_'))
            .forEach((k) => sessionStorage.removeItem(k));
    } catch (e) {}

    // Clear every cookie this page can see (server already expires the session cookie)
    try {
        document.cookie.split(';').forEach(function (c) {
            const name = c.split('=')[0].trim();
            if (!name) return;
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname + ';';
        });
    } catch (e) {}

    sessionPrivateKey = null; sessionPrivateKeyId = null; sessionPassword = null;
    currentLinkId = null;
}

// FORGET THIS TAB – wipe everything THIS browser locally holds for the box
// (cookies, remembered password, cached private key). The box itself and its
// messages on the server are untouched — getting back in from any browser
// afterwards needs the recovery code.
function openForgetTabModal() {
    return new Promise((resolve) => {
        const overlay = document.getElementById('forgetTabModalOverlay');
        const cancelBtn = document.getElementById('forgetTabModalCancel');
        const confirmBtn = document.getElementById('forgetTabModalConfirm');

        function cleanup(result) {
            overlay.style.display = 'none';
            cancelBtn.removeEventListener('click', onCancel);
            confirmBtn.removeEventListener('click', onConfirm);
            overlay.removeEventListener('click', onOverlayClick);
            resolve(result);
        }
        function onCancel() { cleanup(false); }
        function onConfirm() { cleanup(true); }
        function onOverlayClick(e) { if (e.target === overlay) cleanup(false); }

        cancelBtn.addEventListener('click', onCancel);
        confirmBtn.addEventListener('click', onConfirm);
        overlay.addEventListener('click', onOverlayClick);
        overlay.style.display = 'flex';
    });
}

async function forgetThisTab() {
    const ok = await openForgetTabModal();
    if (!ok) return;

    clearAllBrowserTraces();
    showToastMsg('This browser has forgotten this box. Use your recovery code to get back in.', 3500, 'success');
    setTimeout(() => { window.location.reload(); }, 900);
}

function showRestoreView() { showView('view-restore'); }
function goHome() { window.location.href = window.location.pathname; }

// SHARE LINK – copy without server round-trip
function copyShareLink() {
    const inp = document.getElementById('shareUrlInput');
    const link = `${window.location.origin}${window.location.pathname}?send=${currentLinkId}`;
    inp.value = link;
    inp.select();
    inp.setSelectionRange(0, inp.value.length);
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(inp.value).then(() => { showToastMsg('Link copied!', 1800, 'success'); })
        .catch(() => { try { document.execCommand('copy'); showToastMsg('Link copied!', 1800, 'success'); } catch(e) { showToastMsg('Could not copy.', 2500, 'error'); } });
    } else {
        try { document.execCommand('copy'); showToastMsg('Link copied!', 1800, 'success'); } catch(e) { showToastMsg('Could not copy.', 2500, 'error'); }
    }
    inp.setSelectionRange(0, 0);
}

// INSTAGRAM SHARE – deep link with web fallback
function shareToInstagram() {
    if (!currentLinkId) return;
    const shareUrl = `${window.location.origin}${window.location.pathname}?send=${currentLinkId}`;
    const appDeepLink = `instagram-reels://share?content_url=${encodeURIComponent(shareUrl)}`;
    const webFallback = `https://www.instagram.com/reels/share?content_url=${encodeURIComponent(shareUrl)}`;
    window.location.href = appDeepLink;
    const start = Date.now();
    const timer = setTimeout(() => { if (Date.now() - start < 2000 && !document.hidden) { window.location.href = webFallback; } }, 1500);
    window.addEventListener('blur', () => clearTimeout(timer), { once: true });
}

// STORY CARD – pass the message to social-card.php via sessionStorage so
// the plaintext never touches the network or the server.
window.shareToStory = function(text) {
    const trimmed = text.length > 300 ? text.slice(0, 297) + '…' : text;
    try {
        sessionStorage.setItem('sb_social_card_msg', trimmed);
        window.open('social-card.php', '_blank');
    } catch (e) {
        // sessionStorage blocked (private mode, etc.) — URL hash fallback,
        // still fully client-side, still no server round-trip.
        window.open('social-card.php#' + encodeURIComponent(trimmed), '_blank');
    }
};

// IMAGE EXPORT – generate PNG locally
function downloadMessageCard(text) {
    showLoadingOverlay(true);
    setTimeout(() => {
        const W = 1080, SCALE = 2;
        const CARD_MARGIN = 55, CARD_R = 38;
        const BOX_TOP_GAP = 165, BOX_SIDE_PADDING = 58, BOX_BOTTOM_GAP = 110, BOX_R = 30;
        const TEXT_SIDE_PADDING = 50, TEXT_TOP_PADDING = 45;
        const MIN_BOX_H = 290, MAX_BOX_H = 950;

        let cleanText = String(text || '').trim();
        if (!cleanText) cleanText = 'Anonymous message';

        const measureCanvas = document.createElement('canvas');
        measureCanvas.width = 10; measureCanvas.height = 10;
        const mctx = measureCanvas.getContext('2d');
        const CARD_W = W - CARD_MARGIN * 2;
        const BOX_W = CARD_W - BOX_SIDE_PADDING * 2;
        const TEXT_MAX_WIDTH = BOX_W - TEXT_SIDE_PADDING * 2;

        function wrapText(fontSize) {
            mctx.font = `700 ${fontSize}px Arial, Helvetica, sans-serif`;
            function breakLongWord(word) {
                const chunks = []; let chunk = '';
                for (const ch of word) {
                    const testChunk = chunk + ch;
                    if (mctx.measureText(testChunk).width > TEXT_MAX_WIDTH && chunk) { chunks.push(chunk); chunk = ch; } else { chunk = testChunk; }
                }
                if (chunk) chunks.push(chunk);
                return chunks;
            }
            const words = cleanText.split(/\s+/);
            const lines = []; let currentLine = '';
            for (const word of words) {
                const wordPieces = mctx.measureText(word).width > TEXT_MAX_WIDTH ? breakLongWord(word) : [word];
                for (const piece of wordPieces) {
                    const testLine = currentLine ? `${currentLine} ${piece}` : piece;
                    if (mctx.measureText(testLine).width > TEXT_MAX_WIDTH && currentLine) { lines.push(currentLine); currentLine = piece; } else { currentLine = testLine; }
                }
            }
            if (currentLine) lines.push(currentLine);
            return lines;
        }

        let fontSize = 42;
        if (cleanText.length > 60) fontSize = 38;
        if (cleanText.length > 120) fontSize = 34;
        if (cleanText.length > 220) fontSize = 30;
        if (cleanText.length > 360) fontSize = 26;
        if (cleanText.length > 550) fontSize = 23;
        if (cleanText.length > 800) fontSize = 20;

        let lines = wrapText(fontSize);
        let lineHeight = fontSize * 1.38;
        let boxH = Math.max(MIN_BOX_H, lines.length * lineHeight + TEXT_TOP_PADDING * 2);

        while (boxH > MAX_BOX_H && fontSize > 16) {
            fontSize -= 1;
            lines = wrapText(fontSize);
            lineHeight = fontSize * 1.38;
            boxH = Math.max(MIN_BOX_H, lines.length * lineHeight + TEXT_TOP_PADDING * 2);
        }

        let displayLines = lines;
        if (boxH > MAX_BOX_H) {
            boxH = MAX_BOX_H;
            const maxLines = Math.floor((boxH - TEXT_TOP_PADDING * 2) / lineHeight);
            displayLines = lines.slice(0, maxLines);
            let last = displayLines[maxLines - 1] || '';
            last = last.replace(/\s+$/, '').replace(/.{0,2}$/, '…');
            displayLines[maxLines - 1] = last;
        }

        const CARD_H = BOX_TOP_GAP + boxH + BOX_BOTTOM_GAP;
        const H = CARD_H + CARD_MARGIN * 2;

        const canvas = document.createElement('canvas');
        canvas.width = W * SCALE; canvas.height = H * SCALE;
        const ctx = canvas.getContext('2d');
        ctx.scale(SCALE, SCALE);

        function roundedRect(x, y, w, h, r) {
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.lineTo(x + w - r, y);
            ctx.quadraticCurveTo(x + w, y, x + w, y + r);
            ctx.lineTo(x + w, y + h - r);
            ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
            ctx.lineTo(x + r, y + h);
            ctx.quadraticCurveTo(x, y + h, x, y + h - r);
            ctx.lineTo(x, y + r);
            ctx.quadraticCurveTo(x, y, x + r, y);
            ctx.closePath();
        }

        function drawBubble(x, y, radius, opacity) {
            ctx.save(); ctx.beginPath(); ctx.arc(x, y, radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${opacity})`; ctx.filter = 'blur(1px)'; ctx.fill(); ctx.restore();
        }

        const bg = ctx.createLinearGradient(0, 0, W, H);
        bg.addColorStop(0, '#4f46e5'); bg.addColorStop(0.48, '#7c3aed'); bg.addColorStop(1, '#c026d3');
        ctx.fillStyle = bg; ctx.fillRect(0, 0, W, H);

        const glow1 = ctx.createRadialGradient(150, H * 0.181, 0, 150, H * 0.181, 330);
        glow1.addColorStop(0, 'rgba(255,255,255,0.14)'); glow1.addColorStop(1, 'rgba(255,255,255,0)');
        ctx.fillStyle = glow1; ctx.fillRect(0, 0, W, H);

        const glow2 = ctx.createRadialGradient(930, H * 0.792, 0, 930, H * 0.792, 350);
        glow2.addColorStop(0, 'rgba(255,255,255,0.10)'); glow2.addColorStop(1, 'rgba(255,255,255,0)');
        ctx.fillStyle = glow2; ctx.fillRect(0, 0, W, H);

        drawBubble(35, H * 0.208, 90, 0.07); drawBubble(1000, H * 0.167, 130, 0.06);
        drawBubble(850, H * 0.056, 45, 0.08); drawBubble(1050, H * 0.847, 150, 0.07);
        drawBubble(210, H * 0.944, 110, 0.06);

        const CARD_X = CARD_MARGIN, CARD_Y = CARD_MARGIN;
        ctx.save(); ctx.shadowColor = 'rgba(0,0,0,0.30)'; ctx.shadowBlur = 45; ctx.shadowOffsetY = 18;
        roundedRect(CARD_X, CARD_Y, CARD_W, CARD_H, CARD_R);
        ctx.fillStyle = 'rgba(255,255,255,0.04)'; ctx.fill(); ctx.restore();

        const cardGradient = ctx.createLinearGradient(CARD_X, CARD_Y, CARD_X + CARD_W, CARD_Y + CARD_H);
        cardGradient.addColorStop(0, 'rgba(255,255,255,0.055)'); cardGradient.addColorStop(1, 'rgba(0,0,0,0.08)');
        roundedRect(CARD_X, CARD_Y, CARD_W, CARD_H, CARD_R);
        ctx.fillStyle = cardGradient; ctx.fill();
        roundedRect(CARD_X, CARD_Y, CARD_W, CARD_H, CARD_R);
        ctx.strokeStyle = 'rgba(255,255,255,0.13)'; ctx.lineWidth = 2; ctx.stroke();

        ctx.textAlign = 'center';
        const titleGradient = ctx.createLinearGradient(330, 0, 750, 0);
        titleGradient.addColorStop(0, '#ffffff'); titleGradient.addColorStop(1, '#eee9ff');
        ctx.font = '800 46px Arial, Helvetica, sans-serif';
        ctx.fillStyle = titleGradient; ctx.shadowColor = 'rgba(0,0,0,0.16)'; ctx.shadowBlur = 12;
        ctx.fillText('Secret Gate', W / 2, CARD_Y + 75);
        ctx.shadowBlur = 0;
        ctx.font = '500 22px Arial, Helvetica, sans-serif';
        ctx.fillStyle = 'rgba(255,255,255,0.78)';
        ctx.fillText('End to end encrypted', W / 2, CARD_Y + 111);

        const BOX_X = CARD_X + BOX_SIDE_PADDING, BOX_Y = CARD_Y + BOX_TOP_GAP, BOX_H = boxH;
        ctx.save(); ctx.shadowColor = 'rgba(0,0,0,0.17)'; ctx.shadowBlur = 28; ctx.shadowOffsetY = 12;
        roundedRect(BOX_X, BOX_Y, BOX_W, BOX_H, BOX_R);
        const messageBoxGradient = ctx.createLinearGradient(BOX_X, BOX_Y, BOX_X + BOX_W, BOX_Y + BOX_H);
        messageBoxGradient.addColorStop(0, 'rgba(255,255,255,0.96)'); messageBoxGradient.addColorStop(1, 'rgba(248,250,252,0.91)');
        ctx.fillStyle = messageBoxGradient; ctx.fill(); ctx.restore();
        roundedRect(BOX_X, BOX_Y, BOX_W, BOX_H, BOX_R);
        ctx.strokeStyle = 'rgba(255,255,255,0.55)'; ctx.lineWidth = 1.5; ctx.stroke();

        ctx.font = `700 ${fontSize}px Arial, Helvetica, sans-serif`;
        ctx.fillStyle = '#171725'; ctx.textAlign = 'center';
        const totalTextHeight = displayLines.length * lineHeight;
        const startY = BOX_Y + (BOX_H - totalTextHeight) / 2 + fontSize * 0.85;
        displayLines.forEach((line, index) => { ctx.fillText(line, W / 2, startY + index * lineHeight); });

        ctx.textAlign = 'right';
        ctx.font = '600 17px Arial, Helvetica, sans-serif';
        ctx.fillStyle = 'rgba(255,255,255,0.72)';
        ctx.fillText('ANONYMOUS', CARD_X + CARD_W - 30, CARD_Y + CARD_H - 37);

        canvas.toBlob((blob) => {
            showLoadingOverlay(false);
            if (!blob) { showToastMsg('Unable to create card.', 2500, 'error'); return; }
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = `secretgate_${Date.now()}.png`; // Add your site name here
            document.body.appendChild(a); a.click(); a.remove();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
            showToastMsg('Downloaded!', 2500, 'success');
        }, 'image/png');
    }, 300);
}

// INIT – wait for load to avoid missing elements
window.addEventListener('load', function() {
    setTimeout(function() {
        var loader = document.getElementById('loader-wrapper');
        var mainContent = document.getElementById('main-wrapper');
        if (mainContent) mainContent.style.display = 'flex';
        initSecurity();
        initAppView();
        attachEventListeners();
        renderTurnstileWidget();
        initSocialFab();

        // Only start the donation banner's own open animation once the loading
        // screen has fully faded away. Starting both animations at the same time
        // makes the banner look half-rendered / cut off while it's still
        // fighting the loader's fade-out for the same pixels.
        function revealDonationBanner() {
            if (bannerRevealed) return;
            bannerRevealed = true;
            if (loader) loader.removeEventListener('transitionend', onLoaderFadeEnd);
            showDonationBanner();
        }
        var bannerRevealed = false;
        function onLoaderFadeEnd(e) {
            if (e.target !== loader) return;
            if (e.propertyName !== 'opacity' && e.propertyName !== 'visibility') return;
            revealDonationBanner();
        }
        if (loader) {
            loader.classList.add('hidden');
            loader.addEventListener('transitionend', onLoaderFadeEnd);
            // Fallback in case transitionend never fires (reduced-motion, etc.)
            setTimeout(revealDonationBanner, 700);
        } else {
            revealDonationBanner();
        }
    }, 1000);
});

// RADIAL MENU – keep icons visible on small screens
function initSocialFab() {
    var wrap = document.getElementById('socialFabWrap');
    var btn = document.getElementById('followUsBtn');
    var bar = document.getElementById('socialBar');
    if (!wrap || !btn || !bar) return;

    var icons = Array.prototype.slice.call(bar.querySelectorAll('a'));
    var margin = 10;
    var desiredRadius = 92;
    var minRadius = 54;


    function positionIcons() {
        var rect = btn.getBoundingClientRect();
        var iconSize = icons.length ? icons[0].getBoundingClientRect().width || 38 : 38;
        var half = iconSize / 2;

        var spaceRight = window.innerWidth - rect.right + rect.width / 2 - half - margin;
        var spaceUp = rect.top + rect.height / 2 - half - margin;
        var spaceLeft = rect.left + rect.width / 2 - half - margin;

        var startAngle = -90;
        var endAngle = 0;
        var radius = Math.max(minRadius, Math.min(desiredRadius, spaceUp, spaceRight));

        if (spaceRight < minRadius && spaceLeft > spaceRight) {
            startAngle = -180;
            endAngle = -90;
            radius = Math.max(minRadius, Math.min(desiredRadius, spaceUp, spaceLeft));
        }

        icons.forEach(function (icon, i) {
            var angle = icons.length === 1
                ? (startAngle + endAngle) / 2
                : startAngle + (endAngle - startAngle) * (i / (icons.length - 1));
            var rad = (angle * Math.PI) / 180;
            var x = Math.cos(rad) * radius;
            var y = Math.sin(rad) * radius;
            icon.style.setProperty('--x', x.toFixed(1) + 'px');
            icon.style.setProperty('--y', y.toFixed(1) + 'px');
        });
    }

    positionIcons();

    var resizeTimer = null;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(positionIcons, 120);
    });
    window.addEventListener('orientationchange', function () {
        setTimeout(positionIcons, 200);
    });

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

    document.addEventListener('click', function (e) {
        if (!wrap.contains(e.target)) setOpen(false);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setOpen(false);
    });
}

// EVENT WIRING – attach after views exist
function attachEventListeners() {
    document.getElementById('btn-create-link').addEventListener('click', generateNewLink);
    document.getElementById('btn-restore-view').addEventListener('click', showRestoreView);
    document.getElementById('url-display').addEventListener('click', copyShareLink);
    document.getElementById('btn-check-inbox').addEventListener('click', fetchMessages);
    const refreshInboxBtn = document.getElementById('btn-refresh-inbox');
    if (refreshInboxBtn) refreshInboxBtn.addEventListener('click', fetchMessages);
    document.getElementById('btn-instagram').addEventListener('click', shareToInstagram);
    document.getElementById('btn-show-recovery').addEventListener('click', showRecoveryCode);
    document.getElementById('btn-auto-delete-settings').addEventListener('click', openAutoDeleteModal);
    document.getElementById('btn-delete-link').addEventListener('click', deletePermanentLink);
    document.getElementById('btn-forget-tab').addEventListener('click', forgetThisTab);
    document.getElementById('btn-send-secret').addEventListener('click', submitSecretMessage);
    document.getElementById('btn-cancel-send').addEventListener('click', goHome);
    document.getElementById('btn-back-dashboard').addEventListener('click', showDashboard);
    document.getElementById('btn-perform-restore').addEventListener('click', performRestore);
    document.getElementById('btn-cancel-restore').addEventListener('click', goHome);
    const promptRandomBtn = document.getElementById('promptRandomBtn');
    if (promptRandomBtn) promptRandomBtn.addEventListener('click', showRandomPrompt);
    const invalidLinkHomeBtn = document.getElementById('btn-invalid-link-home');
    if (invalidLinkHomeBtn) invalidLinkHomeBtn.addEventListener('click', goHome);
}

// BFCACHE – clear sender state after back navigation
window.addEventListener('pageshow', function(event) {
    if (!event.persisted) return;
    const box = document.getElementById('messageToSend');
    if (box) box.value = '';
    resetPromptChips();
    if (typeof turnstile !== 'undefined' && turnstileWidgetId !== null) turnstile.reset(turnstileWidgetId);
});

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================

</script>
</body>
</html>