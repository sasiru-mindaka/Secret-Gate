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


if (!defined('CONFIG_LOADED')) {
    http_response_code(403);
    exit;
}
?>
<style nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
    #siteFooter {
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px;
        padding: 14px 20px 22px; font-family: var(--font-mono);
        color: var(--text-muted); flex-shrink: 0; text-align: center;
    }
    .footer-brand {
        font-size: 0.78rem; font-weight: 600; letter-spacing: 0.4px;
        color: var(--text); order: -1;
    }
    .footer-links { display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap; font-size: 0.68rem; }
    #siteFooter a { color: var(--text-muted); text-decoration: underline; text-underline-offset: 2px; }
    #siteFooter a:hover { color: var(--accent); }
    .footer-dot { opacity: 0.5; }
    @media (max-width: 380px) {
        #siteFooter { padding: 12px 14px 18px; gap: 5px; }
        .footer-brand { font-size: 0.72rem; }
        .footer-links { font-size: 0.62rem; gap: 8px; }
    }
</style>

<footer id="siteFooter">
    <span class="footer-brand">© Secret Gate</span>
    <div class="footer-links">
        <a href="privacy.php">Privacy Policy</a>
        <span class="footer-dot">·</span>
        <a href="terms.php">Terms & Conditions</a>
    </div>
</footer>