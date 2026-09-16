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
    .donation-banner {
        display: flex; align-items: center; justify-content: center; gap: 12px;
        width: 100%; max-height: 0; padding: 0 16px; overflow: hidden; opacity: 0;
        background: linear-gradient(90deg, rgba(71,165,255,0.12), rgba(255,143,0,0.08));
        border-bottom: 1px solid transparent; font-family: var(--font-mono); font-size: 0.75rem;
        color: var(--text-muted); text-align: center; position: relative; z-index: 20;
        flex-wrap: wrap; pointer-events: none; flex-shrink: 0; box-sizing: border-box;
        transition: max-height 0.35s ease, opacity 0.25s ease, padding 0.35s ease, border-color 0.35s ease;
    }
    /* Generous caps so wrapped text (2 lines + close button) never gets clipped by
       the max-height transition; once the transition finishes, JS switches this to
       max-height:none so the banner can never be cut off regardless of content
       reflow (font scaling, zoom, orientation change, etc). */
    .donation-banner.visible { max-height: 200px; padding: 7px 14px; opacity: 1; border-bottom-color: var(--border-soft); pointer-events: auto; }
    .donation-banner.expanded { max-height: none; overflow: visible; }
    .donation-banner .donation-banner-text { color: var(--text); }
    .donation-banner a.donation-banner-link { color: var(--accent); font-weight: 600; text-decoration: none; border-bottom: 1px dashed var(--accent-soft); }
    .donation-banner a.donation-banner-link:hover { color: var(--text); border-bottom-color: var(--text); }
    .donation-banner .donation-banner-close { background: none; border: none; color: var(--text-muted); font-size: 1rem; line-height: 1; cursor: pointer; padding: 4px 6px; border-radius: 6px; margin-left: 4px; }
    .donation-banner .donation-banner-close:hover { color: var(--text); background: rgba(255,255,255,0.06); }
    .donation-banner-text .short { display: none; }

    @media (max-width: 600px) {
        .donation-banner { font-size: 0.62rem; gap: 7px; line-height: 1.25; }
        .donation-banner.visible { max-height: 140px; padding: 5px 10px; }
        .donation-banner-close { font-size: 0.85rem !important; padding: 3px 5px !important; }
        .donation-banner-link { white-space: nowrap; }
    }
    @media (max-width: 420px) {
        .donation-banner-text .full { display: none; }
        .donation-banner-text .short { display: inline; }
        .donation-banner.visible { max-height: 100px; padding: 5px 8px; }
        .donation-banner { font-size: 0.58rem; gap: 5px; }
        .donation-banner-link { font-size: 0.58rem; }
    }
</style>

<div class="donation-banner" id="donationBanner">
    <span class="donation-banner-text"><span class="full">💙 Secret Gate runs on donations — no ads, no tracking.</span><span class="short">💙 We run on donations</span></span>
    <a class="donation-banner-link" id="donationLink" href="donate.php" target="_blank" rel="noopener noreferrer">Support us</a>
    <button type="button" class="donation-banner-close" id="donationClose" aria-label="Dismiss">✕</button>
</div>

<script nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
(function () {
    // GLOBAL ACCESS – let other scripts trigger the banner
    var donationBannerShown = false;
    function showDonationBanner() {
        var banner = document.getElementById('donationBanner');
        if (!banner || donationBannerShown) return;
        donationBannerShown = true;
        banner.classList.add('visible');

        // Once the open transition finishes, drop the max-height cap entirely so the
        // banner can never end up visually clipped, no matter how the content
        // reflows afterwards (dynamic mobile toolbar resize, zoom, rotation, etc).
        banner.addEventListener('transitionend', function onOpen(e) {
            if (e.propertyName !== 'max-height') return;
            if (banner.classList.contains('visible')) banner.classList.add('expanded');
            banner.removeEventListener('transitionend', onOpen);
        });

        var closeBtn = document.getElementById('donationClose');
        var link = document.getElementById('donationLink');
        function dismiss() {
            banner.classList.remove('expanded');
            // Force layout to read the real content height back into max-height
            // before removing 'visible', so the closing transition has something
            // concrete to animate from instead of jumping straight from 'none'.
            banner.style.maxHeight = banner.scrollHeight + 'px';
            requestAnimationFrame(function () {
                banner.classList.remove('visible');
                banner.style.maxHeight = '';
            });
        }
        if (closeBtn) closeBtn.addEventListener('click', dismiss, { once: true });
        if (link) link.addEventListener('click', dismiss, { once: true });
    }
    window.showDonationBanner = showDonationBanner;
})();

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================

</script>