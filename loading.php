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


// ACCESS CONTROL – only allow inclusion from whitelisted scripts
$allowed_scripts = [
    'index.php',
    'social-card.php',
    'feedback.php',
    'privacy.php',
    'terms.php',
    'donate.php',
];

$current_script = basename($_SERVER['SCRIPT_NAME'] ?? '');

// Blocking access from scripts not on the whitelist
if (!in_array($current_script, $allowed_scripts, true)) {
    http_response_code(403);
    include __DIR__ . '/403.php';
    exit;
}

?>
<style nonce="<?php echo htmlspecialchars($csp_nonce ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    #loader-wrapper {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: #0d0d0d;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        transition: opacity 0.6s ease, visibility 0.6s ease;
    }
    #loader-wrapper.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    .loader-text {
        color: #f8f8f8;
        font-size: clamp(1.5rem, 5vw, 2.5rem);
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        text-align: center;
        white-space: nowrap;
    }
    .loader-bar {
        display: block;
        --height-of-loader: 5px;
        --loader-color: #ffffff;
        width: 200px;
        max-width: 70vw;
        height: var(--height-of-loader);
        border-radius: 30px;
        background-color: #000000;
        position: relative;
        overflow: hidden;
    }
    .loader-bar::before {
        content: "";
        position: absolute;
        background: var(--loader-color);
        top: 0;
        left: 0;
        width: 0%;
        height: 100%;
        border-radius: 30px;
        animation: loadingMove 1s ease-in-out infinite;
    }
    @keyframes loadingMove {
        50% { width: 100%; }
        100% { width: 0; right: 0; left: unset; }
    }
</style>

<div id="loader-wrapper">
    <div class="loader-text">Secret Gate</div>
    <div class="loader-bar"></div>
</div>