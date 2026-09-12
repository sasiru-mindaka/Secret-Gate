<?php


/**
 * SECRET GATE - ZERO-KNOWLEDGE EPHEMERAL MESSAGING ENGINE
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

if (!isset($csp_nonce)) {
    $csp_nonce = defined('CSP_NONCE') ? CSP_NONCE : base64_encode(random_bytes(16));
}

http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 – Access Denied | SecretBox</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" integrity="sha512-7eHRwcbYkK4d9g/6tD/mhkf++eoTHwpNM9woBxtmtKQu8TiXCHybz5G3fK2iKMreG49adK6U3bh22Rbc2Cf1+A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="icon" type="image/png" href="./images/secret_gate_logo.png">
            <style nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
        body {
            background: #020408;
            color: #e0e6ed;
            font-family: 'Inter', -apple-system, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .container {
            max-width: 500px;
            padding: 20px;
        }
        h1 { font-size: 5rem; margin-bottom: 0.5rem; }
        p { color: #8e8e93; }
        a { color: #7aa2f7; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>403</h1>
        <p>Access to this resource is forbidden.</p>
        <a href="https://secretgate.site">Return to Homepage</a> <!-- Add your domain here -->
    </div>
</body>
</html>
<?php exit();

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================

?>