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

// METHOD GUARD – this page is GET-only now; the message travels via
// sessionStorage on the client, never as a POST body, so it's never written
// to PHP's (disk-backed) session storage.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(403);
    include __DIR__ . '/403.php';
    exit();
}

// The message itself is read client-side from sessionStorage (see the
// inline script below); the server never receives or stores it, so there's
// no PHP variable for it here.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Card Studio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:ital,wght@0,200;0,300;0,400;0,500;0,600;1,400&family=Oxanium:wght@400;600;700;800&family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@400;600;700&family=Bebas+Neue&family=Righteous&family=Nunito:wght@300;400;600;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="icon" type="image/png" href="./images/secret_gate_logo.png">

<style nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

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
        --accent-soft: rgba(71, 165, 255, 0.12);
        --accent-glow: rgba(71, 165, 255, 0.25);
        --amber: #FF8F00;
        --danger: #ff4b6e;
        --success: #9ece6a;
        --radius-lg: 28px;
        --radius-md: 22px;
        --radius-sm: 16px;

        --font-display: 'Space Grotesk', sans-serif;
        --font-body: 'Space Grotesk', sans-serif;
        --font-mono: 'JetBrains Mono', monospace;
    }

    html, body { height: 100%; overflow: hidden; }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: var(--font-body);
        display: flex;
        flex-direction: column;
    }

    @font-face { font-family: 'Cikeregular'; src: url('./fonts/cikeregular.otf') format('opentype'); font-weight: 400; font-style: normal; font-display: swap; }
    @font-face { font-family: 'NotoColorEmoji'; src: url('./fonts/NotoColorEmoji-Regular.ttf') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
    @font-face { font-family: 'Clouds'; src: url('./fonts/clouds.ttf') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
    @font-face { font-family: 'GrindyBrush'; src: url('./fonts/grindy-brush.otf') format('opentype'); font-weight: 400; font-style: normal; font-display: swap; }
    @font-face { font-family: 'LeagueSpartan'; src: url('./fonts/LeagueSpartan-Bold.otf') format('opentype'); font-weight: 700; font-style: normal; font-display: swap; }
    @font-face { font-family: 'Montserrat'; src: url('./fonts/montserrat-black.otf') format('opentype'); font-weight: 900; font-style: normal; font-display: swap; }
    @font-face { font-family: 'TypogamaAhsing'; src: url('./fonts/typogama-ahsing.otf') format('opentype'); font-weight: 400; font-style: normal; font-display: swap; }

    #header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 18px; height: 58px;
        background: rgba(0,0,0,0.97);
        border-bottom: 1px solid var(--border);
        flex-shrink: 0; position: relative; z-index: 200;
    }
    #back-btn {
        display: flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border); border-radius: 30px;
        padding: 7px 15px; color: var(--text-muted);
        font-family: var(--font-body); font-size: 0.8rem;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    #back-btn:hover { color: var(--text); background: rgba(255,255,255,0.09); }
    #header-title {
        font-family: var(--font-display); font-size: 1rem; font-weight: 700; letter-spacing: -0.3px;
        background: linear-gradient(135deg, #ffffff 0%, var(--accent) 55%, var(--amber) 120%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    #download-btn {
        display: flex; align-items: center; gap: 7px;
        background: var(--accent); color: #03070f; border: none;
        border-radius: 30px; padding: 9px 18px;
        font-family: var(--font-body); font-size: 0.8rem; font-weight: 700;
        cursor: pointer; transition: all 0.2s; letter-spacing: 0.2px;
    }
    #download-btn:hover { background: #7fc0ff; transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }
    #download-btn:active { transform: translateY(0); }
    #download-btn.busy { opacity: 0.55; pointer-events: none; }

    #main { display: flex; flex: 1; overflow: hidden; min-height: 0; }

    #card-area {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: flex-start;
        padding: 20px 20px 14px; overflow-y: auto; overflow-x: hidden; gap: 14px;
        position: relative;
    }
    #card-area::before {
        content: ''; position: fixed; inset: 0; z-index: -1; pointer-events: none;
        background: radial-gradient(circle at 22% 20%, rgba(71,165,255,0.06), transparent 40%),
                    radial-gradient(circle at 80% 85%, rgba(255,143,0,0.04), transparent 40%);
    }

    .format-bar { display: flex; gap: 6px; flex-shrink: 0; }
    .fmt-pill {
        background: rgba(255,255,255,0.04); border: 1px solid var(--border);
        border-radius: 30px; padding: 6px 16px; font-size: 0.75rem;
        color: var(--text-muted); cursor: pointer; transition: all 0.2s; letter-spacing: 0.3px;
        font-family: var(--font-mono);
    }
    .fmt-pill.on { background: var(--accent-soft); border-color: rgba(71,165,255,0.4); color: var(--accent); }

    #card-wrap { position: relative; flex-shrink: 0; }

    #card {
        position: relative; overflow: hidden; cursor: default;
        box-shadow: 0 30px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.06);
        transition: background 0.25s ease;
    }
    #card-glow1, #card-glow2 { position: absolute; border-radius: 50%; pointer-events: none; z-index: 0; filter: blur(30px); }
    #card-glow1 { top: -8%; left: -4%; width: 46%; height: 30%; }
    #card-glow2 { bottom: -10%; right: -6%; width: 50%; height: 32%; }
    .card-bubble { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.08); filter: blur(1px); pointer-events: none; z-index: 0; }

    #card-title {
        position: absolute; left: 0; right: 0; text-align: center;
        z-index: 6; pointer-events: none; user-select: none; font-weight: 800;
        letter-spacing: -0.3px; white-space: nowrap;
    }
    #card-subtitle {
        position: absolute; left: 0; right: 0; text-align: center;
        z-index: 6; pointer-events: none; user-select: none; font-weight: 500;
        white-space: nowrap;
    }
    #card-footer {
        position: absolute; z-index: 6; pointer-events: none; user-select: none;
        font-family: var(--font-mono); letter-spacing: 2px; text-transform: uppercase;
        white-space: nowrap;
    }

    #msgbox {
        position: absolute; z-index: 7; cursor: default; user-select: none;
        touch-action: auto; transform-origin: center center;
    }
    #msgbox-box {
        border-radius: 30px;
        box-shadow: 0 18px 45px rgba(0,0,0,0.22);
        display: inline-block;
        width: max-content;
        max-width: 80%;
    }
    #msgbox-text { text-align: center; white-space: pre-wrap; word-break: break-word; line-height: 1.4; }

    .ctxt-layer {
        position: absolute; z-index: 9; cursor: move; user-select: none;
        touch-action: none; transform-origin: center center; display: block;
    }
    .ctxt-layer.sel::before {
        content: ''; position: absolute; inset: -8px;
        border: 1.5px dashed rgba(255,143,0,0.6); border-radius: 6px; pointer-events: none;
    }
    .ctxt-span { display: block; text-align: center; white-space: pre-wrap; }

    .rot-handle {
        position: absolute; top: -34px; left: 50%; transform: translateX(-50%);
        width: 22px; height: 22px; background: var(--accent); border-radius: 50%;
        cursor: crosshair; border: 2.5px solid rgba(255,255,255,0.9);
        display: none; align-items: center; justify-content: center; font-size: 11px;
        box-shadow: 0 2px 10px var(--accent-glow); z-index: 20;
    }
    .ctxt-layer.sel .rot-handle { display: flex; }
    #msgbox .rot-handle { display: none !important; }
    .rot-handle::after {
        content: ''; position: absolute; bottom: -12px; left: 50%; transform: translateX(-50%);
        width: 1.5px; height: 10px; background: rgba(71,165,255,0.7);
    }

    #panel {
        width: 300px; background: var(--surface); border-left: 1px solid var(--border);
        display: flex; flex-direction: column; overflow: hidden; flex-shrink: 0;
    }
    #panel-handle-wrap { display: none; }
    #tabs { display: flex; border-bottom: 1px solid var(--border); flex-shrink: 0; }
    .tab {
        flex: 1; padding: 13px 4px; text-align: center; font-size: 0.68rem;
        color: var(--text-muted); cursor: pointer; border-bottom: 2px solid transparent;
        transition: all 0.2s; letter-spacing: 0.3px; font-family: var(--font-mono);
    }
    .tab.on { color: var(--accent); border-bottom-color: var(--accent); }

    #pane { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 20px; -webkit-overflow-scrolling: touch; }
    #pane::-webkit-scrollbar { width: 3px; }
    #pane::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 2px; }

    .tp { display: none; flex-direction: column; gap: 18px; }
    .tp.on { display: flex; }

    .sec-lbl {
        font-family: var(--font-mono); font-size: 0.6rem; letter-spacing: 2px;
        text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;
        display: flex; align-items: center; gap: 6px;
    }

    .th-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 7px; }
    .th-sw {
        aspect-ratio: 1; border-radius: 10px; cursor: pointer; border: 2px solid transparent;
        transition: all 0.2s; position: relative; overflow: hidden;
    }
    .th-sw.on { border-color: rgba(255,255,255,0.9); transform: scale(1.07); box-shadow: 0 4px 14px rgba(0,0,0,0.5); }
    .th-sw span {
        position: absolute; bottom: 0; left: 0; right: 0; padding: 2px 1px 3px;
        font-size: 6.3px; text-align: center; background: rgba(0,0,0,0.6); color: rgba(255,255,255,0.85);
    }

    .custom-color-row {
        display: flex; align-items: center; gap: 10px;
        border: 1px dashed var(--border-soft); border-radius: 12px; padding: 10px 12px;
    }
    .custom-color-row input[type="color"] { width: 34px; height: 34px; border: 2px solid var(--border); border-radius: 50%; padding: 0; cursor: pointer; background: none; flex-shrink: 0; }
    .custom-color-row .cc-label { font-size: 0.78rem; color: var(--text-muted); }
    .custom-color-row .cc-label b { color: var(--text); display: block; font-size: 0.82rem; }

    .fn-list { display: flex; flex-direction: column; gap: 5px; }
    .fn-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border);
        cursor: pointer; transition: all 0.2s; min-height: 40px; touch-action: manipulation;
    }
    .fn-item.on { border-color: rgba(71,165,255,0.45); background: var(--accent-soft); }
    .fn-preview { font-size: 0.9rem; color: var(--text); }
    .fn-name { font-size: 0.65rem; color: var(--text-muted); }

    .sl { width: 100%; accent-color: var(--accent); cursor: pointer; height: 28px; touch-action: manipulation; }
    .sl::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 20px; height: 20px; border-radius: 50%; background: var(--accent); cursor: pointer; margin-top: -8px; }
    .sl::-webkit-slider-runnable-track { height: 4px; border-radius: 2px; }
    .sl::-moz-range-thumb { width: 20px; height: 20px; border-radius: 50%; background: var(--accent); border: none; cursor: pointer; }
    .sl-row { display: flex; align-items: center; gap: 10px; }
    .sl-val { font-family: var(--font-mono); font-size: 0.7rem; color: var(--text-muted); min-width: 34px; text-align: right; }

    .cl-row { display: flex; gap: 7px; flex-wrap: wrap; align-items: center; }
    .cl-dot { width: 28px; height: 28px; border-radius: 50%; cursor: pointer; border: 2px solid transparent; transition: all 0.18s; flex-shrink: 0; touch-action: manipulation; }
    .cl-dot.on { border-color: white; transform: scale(1.15); }
    .cl-dot.dark { border-color: rgba(255,255,255,0.2); }
    input[type="color"] { width: 32px; height: 32px; border: 2px solid var(--border); border-radius: 50%; padding: 0; cursor: pointer; background: none; }

    .ctrl-ta {
        width: 100%; background: rgba(0,0,0,0.35); border: 1px solid var(--border);
        border-radius: 10px; padding: 10px 12px; color: var(--text);
        font-family: var(--font-body); font-size: 0.85rem; outline: none; transition: 0.2s; resize: none;
    }
    .ctrl-ta:focus { border-color: rgba(71,165,255,0.5); box-shadow: 0 0 0 2px var(--accent-glow); }

    .cb { width: 100%; padding: 10px 14px; border-radius: 10px; font-family: var(--font-body); font-size: 0.82rem; font-weight: 600; border: 1px solid var(--border); cursor: pointer; transition: all 0.2s; min-height: 42px; touch-action: manipulation; }
    .cb-p { background: var(--accent); color: #03070f; border-color: transparent; }
    .cb-p:hover { background: #7fc0ff; }
    .cb-d { background: rgba(255,75,110,0.08); color: var(--danger); }
    .cb-d:hover { background: rgba(255,75,110,0.16); }
    .cb-o { background: transparent; color: var(--text-muted); }
    .cb-o:hover { background: rgba(255,255,255,0.05); color: var(--text); }

    .style-row { display: flex; gap: 6px; flex-wrap: wrap; }
    .style-btn {
        padding: 8px 14px; border-radius: 8px; font-family: var(--font-body); font-size: 0.78rem;
        border: 1px solid var(--border); cursor: pointer; transition: all 0.2s; background: transparent; color: var(--text-muted);
        min-height: 38px; touch-action: manipulation;
    }
    .style-btn.on { background: var(--accent-soft); border-color: rgba(71,165,255,0.4); color: var(--accent); }

    .layer-list { display: flex; flex-direction: column; gap: 6px; }
    .layer-chip {
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
        padding: 9px 10px; border-radius: 10px; border: 1px solid var(--border);
        cursor: pointer; transition: all 0.2s;
    }
    .layer-chip.on { border-color: rgba(255,143,0,0.5); background: rgba(255,143,0,0.08); }
    .layer-chip .lc-text { font-size: 0.78rem; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; }
    .layer-chip .lc-del { font-size: 0.75rem; color: var(--text-muted); cursor: pointer; flex-shrink: 0; }
    .layer-chip .lc-del:hover { color: var(--danger); }
    .layer-empty { font-size: 0.76rem; color: var(--text-muted); font-family: var(--font-mono); text-align: center; padding: 14px 6px; border: 1px dashed var(--border-soft); border-radius: 10px; }

    #toast {
        position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(70px);
        background: rgba(17,19,23,0.97); backdrop-filter: blur(20px);
        border: 1px solid rgba(71,165,255,0.25); border-radius: 60px;
        padding: 10px 22px; font-size: 0.8rem; color: var(--text);
        transition: 0.3s cubic-bezier(0.2,0.8,0.2,1); opacity: 0; z-index: 9999;
        white-space: nowrap; pointer-events: none; font-family: var(--font-body);
    }
    #toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }

    @media (max-width: 760px) {
        #header { padding: 0 12px; height: 56px; }
        #header-title { font-size: 0.86rem; }
        #back-btn { padding: 8px 14px; font-size: 0.74rem; min-height: 40px; touch-action: manipulation; }
        #download-btn { padding: 10px 16px; font-size: 0.76rem; min-height: 40px; touch-action: manipulation; }

        #main { flex-direction: column; }
        #card-area { padding: 12px 10px 6px; flex: 1 1 auto; min-height: 0; }
        #card-wrap { max-width: 100%; }

        #panel {
            width: 100%; border-left: none;
            border-top: 1px solid var(--border);
            border-radius: 18px 18px 0 0;
            max-height: 46dvh; flex-shrink: 0;
            box-shadow: 0 -12px 30px rgba(0,0,0,0.4);
            transition: max-height 0.15s ease;
        }
        #panel-handle-wrap {
            display: flex; justify-content: center; align-items: center;
            padding: 8px 0 6px; cursor: grab; touch-action: none; flex-shrink: 0;
        }
        #panel-handle-wrap:active { cursor: grabbing; }
        #panel-handle { width: 36px; height: 4px; border-radius: 4px; background: var(--border); }
        #panel-handle-wrap.dragging #panel-handle { background: var(--accent); }
        #pane { padding: 12px; gap: 14px; }
        .tab { padding: 13px 2px; font-size: 0.66rem; min-height: 44px; touch-action: manipulation; }
        .th-grid { grid-template-columns: repeat(5, 1fr); gap: 8px; }
        .th-sw { min-height: 40px; }
        .fmt-pill { padding: 9px 16px; min-height: 38px; touch-action: manipulation; }
    }

    @media (max-width: 400px) {
        .th-grid { grid-template-columns: repeat(4, 1fr); }
        #panel { max-height: 50dvh; }
    }
</style>
</head>
<body>

<?php include 'loading.php'; ?>


<div id="header">
    <button id="back-btn">← Back</button>
    <div id="header-title">Card Studio</div>
    <button id="download-btn">⬇ Save Card</button>
</div>

<div id="main">

    <div id="card-area">
        <div class="format-bar">
            <button class="fmt-pill on" data-fmt="1:1">■ Post 1:1</button>
            <button class="fmt-pill" data-fmt="9:16">▮ Story 9:16</button>
            <button class="fmt-pill" data-fmt="4:5">▬ Portrait 4:5</button>
        </div>

        <div id="card-wrap">
            <div id="card">
                <div id="card-glow1"></div>
                <div id="card-glow2"></div>
                <div id="card-bubbles"></div>

                <div id="card-title">Secret Gate</div>
                <div id="card-subtitle">End to end encrypted</div>

                <div id="msgbox">
                    <div id="msgbox-box">
                        <div id="msgbox-text"></div>
                    </div>
                </div>

                <div id="ctxt-container"></div>

                <div id="card-footer">Anonymous</div>
            </div>
        </div>
    </div>

    <div id="panel">
        <div id="panel-handle-wrap"><div id="panel-handle"></div></div>
        <div id="tabs">
            <div class="tab on" data-tab="themes">🎨 Theme</div>
            <div class="tab" data-tab="message">💬 Message</div>
            <div class="tab" data-tab="custom">✏️ Add Text</div>
        </div>

        <div id="pane">

            <div class="tp on" id="tp-themes">
                <div>
                    <div class="sec-lbl">Pick a theme</div>
                    <div class="th-grid" id="th-grid"></div>
                </div>
                <div>
                    <div class="sec-lbl">Or choose your own colour</div>
                    <div class="custom-color-row">
                        <input type="color" id="custom-color-picker" value="#7c3aed">
                        <div class="cc-label"><b>Custom colour</b>Builds a matching gradient theme</div>
                    </div>
                </div>
            </div>

            <div class="tp" id="tp-message">
                <div>
                    <div class="sec-lbl">Font</div>
                    <div class="fn-list" id="msg-fn-list"></div>
                </div>
                <div>
                    <div class="sec-lbl">Size</div>
                    <div class="sl-row">
                        <input type="range" class="sl" id="msg-sz" min="9" max="52" value="21">
                        <span class="sl-val" id="msg-sz-v">21px</span>
                    </div>
                </div>
                <div>
                    <div class="sec-lbl">Colour</div>
                    <div class="cl-row" id="msg-cl-row"></div>
                </div>
                <div>
                    <div class="sec-lbl">Style</div>
                    <div class="style-row">
                        <button class="style-btn" data-style="bold"><b>B</b></button>
                        <button class="style-btn" data-style="italic"><i>I</i></button>
                        <button class="style-btn" data-style="glow">✦ Glow</button>
                        <button class="style-btn" data-style="shadow">◈ Shadow</button>
                    </div>
                </div>
            </div>

            <div class="tp" id="tp-custom">
                <div>
                    <div class="sec-lbl">New text</div>
                    <textarea class="ctrl-ta" id="ctxt-input" rows="2" placeholder="Type text to add..."></textarea>
                    <br><br>
                    <button class="cb cb-p" id="add-ctxt-btn">+ Add to card</button>
                </div>
                <div>
                    <div class="sec-lbl">Your text layers</div>
                    <div class="layer-list" id="layer-list"></div>
                </div>
                <div id="ctxt-editor" style="display:none; flex-direction:column; gap:18px;">
                    <div>
                        <div class="sec-lbl">Font</div>
                        <div class="fn-list" id="ctxt-fn-list"></div>
                    </div>
                    <div>
                        <div class="sec-lbl">Size</div>
                        <div class="sl-row">
                            <input type="range" class="sl" id="ctxt-sz" min="10" max="90" value="26">
                            <span class="sl-val" id="ctxt-sz-v">26px</span>
                        </div>
                    </div>
                    <div>
                        <div class="sec-lbl">Colour</div>
                        <div class="cl-row" id="ctxt-cl-row"></div>
                    </div>
                    <div>
                        <div class="sec-lbl">Style</div>
                        <div class="style-row">
                            <button class="style-btn" data-cstyle="bold"><b>B</b></button>
                            <button class="style-btn" data-cstyle="italic"><i>I</i></button>
                        </div>
                    </div>
                    <div>
                        <div class="sec-lbl">Rotation</div>
                        <div class="sl-row">
                            <input type="range" class="sl" id="ctxt-rot" min="-180" max="180" value="0">
                            <span class="sl-val" id="ctxt-rot-v">0°</span>
                        </div>
                    </div>
                    <button class="cb cb-d" id="remove-ctxt-btn">✕ Remove this text</button>
                </div>
            </div>

        </div>
    </div>

</div>

<div id="toast"></div>

<script nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
    // SERVER-INJECTED MESSAGE – avoid reading from URL
    // MESSAGE SOURCE – read from sessionStorage (set by index.php's
    // shareToStory) instead of a server-rendered value, so the plaintext
    // never has to be sent to or stored by the server.
    let SECRET_MSG = '';
    try {
        SECRET_MSG = sessionStorage.getItem('sb_social_card_msg') || '';
        sessionStorage.removeItem('sb_social_card_msg');
    } catch (e) {
        SECRET_MSG = '';
    }
    // HASH FALLBACK – used when sessionStorage was blocked (private mode,
    // etc.) and shareToStory fell back to passing the message via the URL
    // hash instead. The hash never reaches the server (browsers don't send
    // fragments in requests), so this stays fully client-side too.
    if (!SECRET_MSG && location.hash.length > 1) {
        try { SECRET_MSG = decodeURIComponent(location.hash.slice(1)); } catch (e) {}
        history.replaceState(null, '', location.pathname + location.search);
    }
    if (SECRET_MSG.length > 300) {
        SECRET_MSG = SECRET_MSG.slice(0, 297) + '…';
    }
</script>
<script nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
    // TRUSTED TYPES – enforce secure DOM policies
    if (window.trustedTypes && trustedTypes.createPolicy) {
        trustedTypes.createPolicy('default', {
            createHTML: (string) => string,
            createScript: (string) => string,
            createScriptURL: (string) => string
        });
    }

    // THEME DATA – preset gradients
    const THEMES = [
        { id:'violet',   name:'Violet Dream', c:['#4f46e5','#7c3aed','#c026d3'], deg:135, boxDark:false },
        { id:'ocean',    name:'Ocean Breeze', c:['#023e8a','#0077b6','#48cae4'], deg:150, boxDark:false },
        { id:'sunset',   name:'Sunset Blaze', c:['#7c2d12','#ea580c','#fbbf24'], deg:135, boxDark:false },
        { id:'emerald',  name:'Emerald Forest', c:['#022c22','#059669','#6ee7b7'], deg:150, boxDark:false },
        { id:'cherry',   name:'Cherry Blossom', c:['#831843','#db2777','#f9a8d4'], deg:135, boxDark:false },
        { id:'midnight', name:'Midnight Navy', c:['#020617','#0f172a','#334155'], deg:160, boxDark:true },
        { id:'gold',     name:'Golden Hour', c:['#78350f','#d97706','#fde68a'], deg:135, boxDark:false },
        { id:'crimson',  name:'Crimson Fire', c:['#450a0a','#b91c1c','#fb923c'], deg:145, boxDark:false },
        { id:'cyber',    name:'Cyber Neon', c:['#000000','#0f3d3e','#00ff9d'], deg:150, boxDark:true },
        { id:'arctic',   name:'Arctic Ice', c:['#083344','#0891b2','#a5f3fc'], deg:150, boxDark:false },
        { id:'royal',    name:'Royal Gold', c:['#1e1b4b','#4338ca','#f5d061'], deg:135, boxDark:false },
        { id:'candy',    name:'Cotton Candy', c:['#5b21b6','#ec4899','#fbcfe8'], deg:135, boxDark:false },
        { id:'space',    name:'Deep Space', c:['#000000','#1e1b4b','#4c1d95'], deg:150, boxDark:true },
        { id:'coral',    name:'Coral Reef', c:['#7c2d12','#f97316','#5eead4'], deg:130, boxDark:false },
        { id:'slate',    name:'Mono Slate', c:['#0f0f0f','#3f3f46','#a1a1aa'], deg:150, boxDark:true },
        { id:'lavender', name:'Lavender Fields', c:['#3b0764','#8b5cf6','#ddd6fe'], deg:135, boxDark:false },
    ];

    // FONT DATA – shared across tabs
    const FONTS = [
        { id:'Oxanium', preview:'Futuristic', lbl:'Oxanium' },
        { id:'Outfit', preview:'Clean Modern', lbl:'Outfit' },
        { id:"'Playfair Display'", preview:'Elegant Serif', lbl:'Playfair Display' },
        { id:'Poppins', preview:'Friendly', lbl:'Poppins' },
        { id:"'Dancing Script'", preview:'Handwritten', lbl:'Dancing Script' },
        { id:"'Bebas Neue'", preview:'BOLD CAPS', lbl:'Bebas Neue' },
        { id:"'JetBrains Mono'", preview:'Monospace', lbl:'JetBrains Mono' },
        { id:'Righteous', preview:'Retro Style', lbl:'Righteous' },
        { id:'Nunito', preview:'Rounded Soft', lbl:'Nunito' },
        { id:'Cinzel', preview:'Roman Classic', lbl:'Cinzel' },
        { id:'NotoColorEmoji', preview:'😀 Emoji', lbl:'Noto Color Emoji' },
        { id:'Clouds', preview:'Clouds', lbl:'Clouds' },
        { id:'GrindyBrush', preview:'Brush', lbl:'Grindy Brush' },
        { id:'LeagueSpartan', preview:'BOLD', lbl:'League Spartan' },
        { id:'Montserrat', preview:'Black', lbl:'Montserrat' },
        { id:'TypogamaAhsing', preview:'Deco', lbl:'Typogama Ahsing' },
        { id:'Cikeregular', preview:'Cike', lbl:'Cikeregular' },
    ];

    const COLORS = ['#ffffff','#171725','#7aa2f7','#bb9af7','#9ece6a','#ff9e64','#f7768e','#2ac3de','#ffd700','#ff0099','#00ff99','#ff6b35','#ffb347','#c8b6ff'];

    // APP STATE – single source of truth
    let S = {
        theme: THEMES[0],
        fmt: '1:1',
        cW: 370, cH: 370,
        msg: {
            font: 'Oxanium', sz: 21, c: '', bold: false, italic: false, glow: false, shadow: false,
            x: 50, y: 55, rot: 0, autoFit: true
        },
        texts: [],
        nextTextId: 1,
        selected: null
    };

    // BOOTSTRAP – wire up initial state and UI
    function init() {
        document.getElementById('msgbox-text').textContent = SECRET_MSG || '🤫 Someone sent you an anonymous secret...';

        buildThemeGrid();
        buildFontList('msg-fn-list', S.msg.font, f => { S.msg.font = f; document.getElementById('msgbox-text').style.fontFamily = f; });
        buildColorRow('msg-cl-row', null, c => { S.msg.c = c; applyMsgStyle(); });
        renderLayerList();

        calcCardSize();
        applyTheme(THEMES[0]);
        renderBubbles();

        enforceMsgBounds();

        attachEventListeners();
    }

    // EVENT WIRING – bind controls to state
    function attachEventListeners() {
        document.getElementById('back-btn').addEventListener('click', () => history.back());
        document.getElementById('download-btn').addEventListener('click', downloadCard);

        document.querySelectorAll('.fmt-pill').forEach(btn => {
            btn.addEventListener('click', function() { setFmt(this.dataset.fmt, this); });
        });

        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() { goTab(this.dataset.tab, this); });
        });

        document.getElementById('msg-sz').addEventListener('input', function() { S.msg.autoFit = false; setMsgSz(this.value); });

        document.querySelectorAll('#tp-message .style-btn').forEach(btn => {
            btn.addEventListener('click', function() { toggleMsgStyle(this.dataset.style); });
        });

        document.getElementById('add-ctxt-btn').addEventListener('click', addCtxt);

        document.getElementById('ctxt-sz').addEventListener('input', function() { setCtxtSz(this.value); });
        document.getElementById('ctxt-rot').addEventListener('input', function() { setCtxtRot(this.value); });
        document.getElementById('remove-ctxt-btn').addEventListener('click', removeCtxt);
        document.querySelectorAll('#ctxt-editor .style-btn').forEach(btn => {
            btn.addEventListener('click', function() { toggleCtxtStyle(this.dataset.cstyle); });
        });

        document.getElementById('custom-color-picker').addEventListener('input', function() {
            applyCustomColor(this.value);
        });

        window.addEventListener('resize', () => { calcCardSize(); renderBubbles(); enforceMsgBounds(); });

        initPanelDrag();
    }

    // MOBILE BOTTOM SHEET – drag-to-resize panel handle
    function initPanelDrag() {
        const handleWrap = document.getElementById('panel-handle-wrap');
        const panel = document.getElementById('panel');
        if (!handleWrap || !panel) return;

        function isSheetMode() { return window.innerWidth <= 760; }

        let dragging = false, startY = 0, startH = 0;

        handleWrap.addEventListener('pointerdown', e => {
            if (!isSheetMode()) return;
            dragging = true;
            startY = e.clientY;
            startH = panel.getBoundingClientRect().height;
            handleWrap.classList.add('dragging');
            handleWrap.setPointerCapture(e.pointerId);
            e.preventDefault();
        });
        handleWrap.addEventListener('pointermove', e => {
            if (!dragging) return;
            const dy = startY - e.clientY;
            let newH = startH + dy;
            const minH = window.innerHeight * 0.14;
            const maxH = window.innerHeight * 0.86;
            newH = Math.max(minH, Math.min(maxH, newH));
            panel.style.maxHeight = newH + 'px';
            panel.style.height = newH + 'px';
        });
        const endDrag = () => { dragging = false; handleWrap.classList.remove('dragging'); };
        handleWrap.addEventListener('pointerup', endDrag);
        handleWrap.addEventListener('pointercancel', endDrag);

        window.addEventListener('resize', () => {
            if (!isSheetMode()) {
                panel.style.maxHeight = '';
                panel.style.height = '';
            }
        });
    }

    // DRAG + ROTATE HELPERS – shared by message box and text layers
    function positionLayer(el, x, y, rot) {
        el.style.left = x + '%';
        el.style.top = y + '%';
        el.style.transform = `translate(-50%,-50%) rotate(${rot}deg)`;
    }

    function makeDraggable(el, rotHandle, getPos, setPos, useSafeBounds = false) {
        let dragging = false, rotating = false;
        let sx, sy, ox, oy, rcx, rcy;

        el.addEventListener('pointerdown', e => {
            if (rotHandle && e.target === rotHandle) return;
            dragging = true;
            sx = e.clientX; sy = e.clientY;
            const p = getPos(); ox = p.x; oy = p.y;
            el.setPointerCapture(e.pointerId);
            e.preventDefault();
        });
        el.addEventListener('pointermove', e => {
            if (!dragging) return;
            const cr = document.getElementById('card').getBoundingClientRect();
            const p = getPos();
            let nx = ox + (e.clientX - sx) / cr.width * 100;
            let ny = oy + (e.clientY - sy) / cr.height * 100;

            if (useSafeBounds) {
                const bounds = getMsgConstraints(el);
                nx = Math.max(bounds.minX, Math.min(bounds.maxX, nx));
                ny = Math.max(bounds.minY, Math.min(bounds.maxY, ny));
            } else {
                nx = Math.max(2, Math.min(98, nx));
                ny = Math.max(2, Math.min(98, ny));
            }

            setPos({ x: nx, y: ny, rot: p.rot });
        });
        el.addEventListener('pointerup', () => { dragging = false; });

        if (rotHandle) {
            rotHandle.addEventListener('pointerdown', e => {
                rotating = true;
                const br = el.getBoundingClientRect();
                rcx = br.left + br.width / 2; rcy = br.top + br.height / 2;
                rotHandle.setPointerCapture(e.pointerId);
                e.preventDefault(); e.stopPropagation();
            });
            rotHandle.addEventListener('pointermove', e => {
                if (!rotating) return;
                const angle = Math.atan2(e.clientY - rcy, e.clientX - rcx) * 180 / Math.PI + 90;
                const p = getPos();
                setPos({ x: p.x, y: p.y, rot: Math.round(angle) });
            });
            rotHandle.addEventListener('pointerup', () => { rotating = false; });
        }
    }

    // SAFE BOUNDS – keep message box between title and footer
    function getMsgConstraints(el) {
        const card = document.getElementById('card');
        const cardRect = card.getBoundingClientRect();
        const box = el.querySelector('#msgbox-box') || el;
        const boxW = box.offsetWidth;
        const boxH = box.offsetHeight;

        const titleEl = document.getElementById('card-title');
        const subtitleEl = document.getElementById('card-subtitle');
        const titleRect = titleEl.getBoundingClientRect();
        const subtitleRect = subtitleEl.getBoundingClientRect();
        const topSafePx = Math.max(titleRect.bottom, subtitleRect.bottom) - cardRect.top + 10;

        const footerEl = document.getElementById('card-footer');
        const footerRect = footerEl.getBoundingClientRect();
        const bottomSafePx = footerRect.top - cardRect.top - 10;

        const hMarginPx = cardRect.width * 0.05;
        const minXPx = boxW / 2 + hMarginPx;
        const maxXPx = cardRect.width - boxW / 2 - hMarginPx;

        const vMarginPx = cardRect.height * 0.05;
        const minYPx = topSafePx + boxH / 2 + vMarginPx;
        const maxYPx = bottomSafePx - boxH / 2 - vMarginPx;

        const minX = (minXPx / cardRect.width) * 100;
        const maxX = (maxXPx / cardRect.width) * 100;
        const minY = (minYPx / cardRect.height) * 100;
        const maxY = (maxYPx / cardRect.height) * 100;

        if (minY > maxY) {
            const centerY = (topSafePx + bottomSafePx) / 2 / cardRect.height * 100;
            return { minX: Math.min(minX, maxX), maxX: Math.max(minX, maxX), minY: centerY, maxY: centerY };
        }
        return { minX, maxX, minY, maxY };
    }

    function enforceMsgBounds() {
        const el = document.getElementById('msgbox');
        const bounds = getMsgConstraints(el);
        S.msg.x = Math.max(bounds.minX, Math.min(bounds.maxX, S.msg.x));
        S.msg.y = Math.max(bounds.minY, Math.min(bounds.maxY, S.msg.y));
        positionLayer(el, S.msg.x, S.msg.y, S.msg.rot);
    }

    // LABEL AUTOSIZE – shrink title/subtitle/footer to fit
    function fitLabelFont(el, maxWidth, minFont, maxFont) {
        let fontSize = maxFont;
        el.style.fontSize = fontSize + 'px';
        while (fontSize > minFont && el.scrollWidth > maxWidth) {
            fontSize -= 1;
            el.style.fontSize = fontSize + 'px';
        }
        return fontSize;
    }

    // TEXT AUTOSIZE – font then line-height when text overflows
    function shrinkTextToFit(textEl, boxEl, maxBoxW, maxBoxH, minFont, maxFont) {
        let fontSize = maxFont;
        textEl.style.fontSize = fontSize + 'px';

        while (fontSize > minFont && (boxEl.scrollHeight > maxBoxH || boxEl.scrollWidth > maxBoxW + 2)) {
            fontSize -= 1;
            textEl.style.fontSize = fontSize + 'px';
        }

        let lineHeight = 1.4;
        textEl.style.lineHeight = lineHeight;
        while (lineHeight > 1.05 && boxEl.scrollHeight > maxBoxH) {
            lineHeight = Math.round((lineHeight - 0.05) * 100) / 100;
            textEl.style.lineHeight = lineHeight;
        }

        return fontSize;
    }

    // AUTO-FIT – match message box to safe area on load
    function autoFitMsgSize() {
        const textEl = document.getElementById('msgbox-text');
        const boxEl = document.getElementById('msgbox-box');
        if (!textEl || !boxEl) return;

        const card = document.getElementById('card');
        const cardRect = card.getBoundingClientRect();
        const titleRect = document.getElementById('card-title').getBoundingClientRect();
        const subtitleRect = document.getElementById('card-subtitle').getBoundingClientRect();
        const footerRect = document.getElementById('card-footer').getBoundingClientRect();

        const topSafePx = Math.max(titleRect.bottom, subtitleRect.bottom) - cardRect.top + 10;
        const bottomSafePx = footerRect.top - cardRect.top - 10;
        const vMarginPx = cardRect.height * 0.05;
        const maxBoxH = Math.max(36, (bottomSafePx - topSafePx) - vMarginPx * 2);
        const maxBoxW = S.cW * 0.8;

        const MIN_FONT = 9, MAX_FONT = 52;
        const fontSize = shrinkTextToFit(textEl, boxEl, maxBoxW, maxBoxH, MIN_FONT, MAX_FONT);

        S.msg.sz = fontSize;
        const slider = document.getElementById('msg-sz');
        const sliderVal = document.getElementById('msg-sz-v');
        if (slider) slider.value = fontSize;
        if (sliderVal) sliderVal.textContent = fontSize + 'px';
    }

    // MANUAL SIZE CLAMP – never let box cover title/footer
    function clampMsgSizeToSafeArea() {
        const textEl = document.getElementById('msgbox-text');
        const boxEl = document.getElementById('msgbox-box');
        if (!textEl || !boxEl) return;

        const card = document.getElementById('card');
        const cardRect = card.getBoundingClientRect();
        const titleRect = document.getElementById('card-title').getBoundingClientRect();
        const subtitleRect = document.getElementById('card-subtitle').getBoundingClientRect();
        const footerRect = document.getElementById('card-footer').getBoundingClientRect();

        const topSafePx = Math.max(titleRect.bottom, subtitleRect.bottom) - cardRect.top + 10;
        const bottomSafePx = footerRect.top - cardRect.top - 10;
        const vMarginPx = cardRect.height * 0.05;
        const maxBoxH = Math.max(36, (bottomSafePx - topSafePx) - vMarginPx * 2);
        const maxBoxW = S.cW * 0.8;

        const MIN_FONT = 9;
        const fontSize = shrinkTextToFit(textEl, boxEl, maxBoxW, maxBoxH, MIN_FONT, S.msg.sz);

        if (fontSize !== S.msg.sz) {
            S.msg.sz = fontSize;
            const slider = document.getElementById('msg-sz');
            const sliderVal = document.getElementById('msg-sz-v');
            if (slider) slider.value = fontSize;
            if (sliderVal) sliderVal.textContent = fontSize + 'px';
        }
    }

    // MESSAGE SIZE CONTROL – update font and re-clamp
    function setMsgSz(v) {
        S.msg.sz = +v;
        document.getElementById('msgbox-text').style.fontSize = v + 'px';
        document.getElementById('msg-sz-v').textContent = v + 'px';
        clampMsgSizeToSafeArea();
        enforceMsgBounds();
    }

    // MESSAGE STYLE – apply colour/weight/effect
    function applyMsgStyle() {
        const el = document.getElementById('msgbox-text');
        const boxTextC = S.theme.boxDark ? '#f3f4f6' : '#171725';
        el.style.color = S.msg.c || boxTextC;
        el.style.fontWeight = S.msg.bold ? '700' : '400';
        el.style.fontStyle = S.msg.italic ? 'italic' : 'normal';
        const base = S.msg.c || boxTextC;
        el.style.textShadow = S.msg.glow ? `0 0 22px ${base}cc, 0 0 45px ${base}66`
                            : S.msg.shadow ? `2px 3px 12px rgba(0,0,0,0.35)` : 'none';
    }

    // MESSAGE STYLE TOGGLES – glow and shadow are exclusive
    function toggleMsgStyle(t) {
        const btn = document.getElementById('tp-message').querySelector(`[data-style="${t}"]`);
        if (t==='bold') { S.msg.bold = !S.msg.bold; btn.classList.toggle('on', S.msg.bold); }
        if (t==='italic') { S.msg.italic = !S.msg.italic; btn.classList.toggle('on', S.msg.italic); }
        if (t==='glow') { S.msg.glow = !S.msg.glow; S.msg.shadow = false; btn.classList.toggle('on', S.msg.glow); document.querySelector('#tp-message [data-style="shadow"]').classList.remove('on'); }
        if (t==='shadow') { S.msg.shadow = !S.msg.shadow; S.msg.glow = false; btn.classList.toggle('on', S.msg.shadow); document.querySelector('#tp-message [data-style="glow"]').classList.remove('on'); }
        applyMsgStyle();
    }

    // CUSTOM TEXT – add a new draggable layer
    function addCtxt() {
        const input = document.getElementById('ctxt-input');
        const txt = input.value.trim();
        if (!txt) { toast('⚠️ Enter some text first'); return; }

        const layer = {
            id: 'ctxt-' + (S.nextTextId++),
            text: txt, font: 'Outfit', sz: 26, c: '#ffffff', bold: false, italic: false,
            x: 50, y: 30, rot: 0
        };
        S.texts.push(layer);
        input.value = '';

        renderCtxtLayer(layer);
        renderLayerList();
        selectLayer(layer.id);
        toast('Text added — drag it into place');
    }

    // LAYER RENDER – inject text layer into the card
    function renderCtxtLayer(layer) {
        const el = document.createElement('div');
        el.className = 'ctxt-layer';
        el.id = layer.id;
        el.innerHTML = `<div class="rot-handle">↻</div><span class="ctxt-span"></span>`;
        document.getElementById('ctxt-container').appendChild(el);
        updateCtxtLayerStyle(layer);
        positionLayer(el, layer.x, layer.y, layer.rot);

        const rotHandle = el.querySelector('.rot-handle');
        makeDraggable(el, rotHandle,
            () => ({ x: layer.x, y: layer.y, rot: layer.rot }),
            (p) => { layer.x = p.x; layer.y = p.y; layer.rot = p.rot; positionLayer(el, p.x, p.y, p.rot); if (S.selected === layer.id) syncCtxtRotUI(layer); }
        );
        el.addEventListener('pointerdown', () => selectLayer(layer.id));
    }

    // LAYER STYLE SYNC – push layer state to DOM
    function updateCtxtLayerStyle(layer) {
        const el = document.getElementById(layer.id);
        if (!el) return;
        const span = el.querySelector('.ctxt-span');
        span.textContent = layer.text;
        span.style.fontFamily = layer.font;
        span.style.fontSize = layer.sz + 'px';
        span.style.color = layer.c;
        span.style.fontWeight = layer.bold ? '700' : '400';
        span.style.fontStyle = layer.italic ? 'italic' : 'normal';
        span.style.lineHeight = '1.3';
    }

    function getSelectedLayer() { return S.texts.find(t => t.id === S.selected) || null; }

    // LAYER SELECT – focus a layer and open editor
    function selectLayer(id) {
        S.selected = id;
        document.querySelectorAll('.ctxt-layer').forEach(el => el.classList.toggle('sel', el.id === id));
        renderLayerList();

        const layer = getSelectedLayer();
        const editor = document.getElementById('ctxt-editor');
        if (!layer) { editor.style.display = 'none'; return; }
        editor.style.display = 'flex';

        buildFontList('ctxt-fn-list', layer.font, f => { layer.font = f; updateCtxtLayerStyle(layer); });
        document.getElementById('ctxt-sz').value = layer.sz;
        document.getElementById('ctxt-sz-v').textContent = layer.sz + 'px';
        buildColorRow('ctxt-cl-row', layer.c, c => { layer.c = c; updateCtxtLayerStyle(layer); });
        document.querySelectorAll('#ctxt-editor [data-cstyle]').forEach(b => {
            b.classList.toggle('on', (b.dataset.cstyle==='bold' && layer.bold) || (b.dataset.cstyle==='italic' && layer.italic));
        });
        syncCtxtRotUI(layer);

        goTab('custom', document.querySelector('.tab[data-tab="custom"]'));
    }

    function syncCtxtRotUI(layer) {
        document.getElementById('ctxt-rot').value = layer.rot;
        document.getElementById('ctxt-rot-v').textContent = layer.rot + '°';
    }

    function setCtxtSz(v) {
        const layer = getSelectedLayer(); if (!layer) return;
        layer.sz = +v;
        document.getElementById('ctxt-sz-v').textContent = v + 'px';
        updateCtxtLayerStyle(layer);
    }

    function setCtxtRot(v) {
        const layer = getSelectedLayer(); if (!layer) return;
        layer.rot = +v;
        document.getElementById('ctxt-rot-v').textContent = v + '°';
        positionLayer(document.getElementById(layer.id), layer.x, layer.y, layer.rot);
    }

    function toggleCtxtStyle(t) {
        const layer = getSelectedLayer(); if (!layer) return;
        const btn = document.querySelector(`#ctxt-editor [data-cstyle="${t}"]`);
        if (t === 'bold') { layer.bold = !layer.bold; btn.classList.toggle('on', layer.bold); }
        if (t === 'italic') { layer.italic = !layer.italic; btn.classList.toggle('on', layer.italic); }
        updateCtxtLayerStyle(layer);
    }

    function removeCtxt() {
        const layer = getSelectedLayer(); if (!layer) return;
        const el = document.getElementById(layer.id);
        if (el) el.remove();
        S.texts = S.texts.filter(t => t.id !== layer.id);
        S.selected = null;
        document.getElementById('ctxt-editor').style.display = 'none';
        renderLayerList();
        toast('Text removed');
    }

    // LAYER LIST UI – chip per text layer
    function renderLayerList() {
        const c = document.getElementById('layer-list');
        c.innerHTML = '';
        if (S.texts.length === 0) {
            c.innerHTML = `<div class="layer-empty">No custom text yet — add one above</div>`;
            return;
        }
        S.texts.forEach(layer => {
            const chip = document.createElement('div');
            chip.className = 'layer-chip' + (layer.id === S.selected ? ' on' : '');
            chip.innerHTML = `<span class="lc-text">${escapeHtml(layer.text)}</span><span class="lc-del">✕</span>`;
            chip.querySelector('.lc-text').addEventListener('click', () => selectLayer(layer.id));
            chip.querySelector('.lc-del').addEventListener('click', (e) => {
                e.stopPropagation();
                const el = document.getElementById(layer.id);
                if (el) el.remove();
                S.texts = S.texts.filter(t => t.id !== layer.id);
                if (S.selected === layer.id) { S.selected = null; document.getElementById('ctxt-editor').style.display = 'none'; }
                renderLayerList();
            });
            c.appendChild(chip);
        });
    }

    // XSS GUARD – escape user-supplied text for innerHTML
    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    // FORMAT SWITCH – recompute card dimensions
    function setFmt(fmt, btn) {
        S.fmt = fmt;
        document.querySelectorAll('.fmt-pill').forEach(b=>b.classList.remove('on'));
        btn.classList.add('on');
        calcCardSize();
        renderBubbles();
        enforceMsgBounds();
    }

    // TAB SWITCHER – panel tab visibility
    function goTab(id, tabEl) {
        document.querySelectorAll('.tab').forEach(t=>t.classList.remove('on'));
        document.querySelectorAll('.tp').forEach(p=>p.classList.remove('on'));
        tabEl.classList.add('on');
        document.getElementById('tp-'+id).classList.add('on');
    }

    // CARD SIZING – fit chosen format to available space
    function calcCardSize() {
        const area = document.getElementById('card-area');
        const aW = area.clientWidth - 40;
        const aH = area.clientHeight - 60;

        if (S.fmt === '1:1') {
            S.cW = S.cH = Math.min(370, aW, aH);
        } else if (S.fmt === '9:16') {
            S.cH = Math.min(aH, Math.floor(aW * 16/9), 560);
            S.cW = Math.round(S.cH * 9/16);
            if (S.cW > aW) { S.cW = aW; S.cH = Math.round(S.cW * 16/9); }
        } else {
            S.cH = Math.min(aH, Math.floor(aW * 5/4), 500);
            S.cW = Math.round(S.cH * 4/5);
            if (S.cW > aW) { S.cW = aW; S.cH = Math.round(S.cW * 5/4); }
        }

        const card = document.getElementById('card');
        card.style.width = S.cW + 'px';
        card.style.height = S.cH + 'px';
        card.style.borderRadius = Math.max(16, S.cW * 0.07) + 'px';

        const titleSz = Math.max(22, Math.min(48, S.cW * 0.15));
        const title = document.getElementById('card-title');
        title.style.top = Math.max(22, S.cH * 0.055) + 'px';
        const titleMaxW = S.cW - Math.max(20, S.cW * 0.06);
        const fittedTitleSz = fitLabelFont(title, titleMaxW, 10, titleSz);

        const subSz = Math.max(14, S.cW * 0.08);
        const sub = document.getElementById('card-subtitle');
        sub.style.top = (Math.max(22, S.cH * 0.055) + fittedTitleSz + 8) + 'px';
        const subMaxW = S.cW - Math.max(18, S.cW * 0.055);
        fitLabelFont(sub, subMaxW, 8, subSz);

        const footSz = Math.max(8, S.cW * 0.024);
        const footer = document.getElementById('card-footer');
        footer.style.bottom = Math.max(14, S.cH * 0.035) + 'px';
        footer.style.right = Math.max(18, S.cW * 0.06) + 'px';
        const footMaxW = S.cW - Math.max(18, S.cW * 0.06) - 12;
        fitLabelFont(footer, footMaxW, 6, footSz);

        const box = document.getElementById('msgbox-box');
        box.style.padding = Math.max(18, S.cW * 0.075) + 'px ' + Math.max(16, S.cW * 0.06) + 'px';
        box.style.maxWidth = (S.cW * 0.8) + 'px';

        if (S.msg.autoFit) {
            autoFitMsgSize();
        } else {
            document.getElementById('msgbox-text').style.fontSize = S.msg.sz + 'px';
            clampMsgSizeToSafeArea();
        }

        positionLayer(document.getElementById('msgbox'), S.msg.x, S.msg.y, S.msg.rot);
        S.texts.forEach(t => {
            const el = document.getElementById(t.id);
            if (el) positionLayer(el, t.x, t.y, t.rot);
        });

        enforceMsgBounds();
    }

    // THEME APPLICATION – gradient + glass box variant
    function themeGradient(t) {
        return `linear-gradient(${t.deg}deg, ${t.c[0]} 0%, ${t.c[1]} 48%, ${t.c[2]} 100%)`;
    }

    function buildThemeGrid() {
        const g = document.getElementById('th-grid');
        THEMES.forEach((t, i) => {
            const el = document.createElement('div');
            el.className = 'th-sw' + (i===0?' on':'');
            el.style.background = themeGradient(t);
            el.innerHTML = `<span>${t.name}</span>`;
            el.addEventListener('click', () => {
                document.querySelectorAll('.th-sw').forEach(s => s.classList.remove('on'));
                el.classList.add('on');
                applyTheme(t);
            });
            g.appendChild(el);
        });
    }

    function applyTheme(t) {
        S.theme = t;
        const card = document.getElementById('card');
        card.style.background = themeGradient(t);

        document.getElementById('card-glow1').style.background = 'radial-gradient(circle, rgba(255,255,255,0.16), transparent 70%)';
        document.getElementById('card-glow2').style.background = 'radial-gradient(circle, rgba(255,255,255,0.12), transparent 70%)';

        const title = document.getElementById('card-title');
        title.style.background = 'linear-gradient(90deg, #ffffff 0%, #eee9ff 100%)';
        title.style.webkitBackgroundClip = 'text'; title.style.webkitTextFillColor = 'transparent'; title.style.backgroundClip = 'text';

        document.getElementById('card-subtitle').style.color = 'rgba(255,255,255,0.78)';
        document.getElementById('card-footer').style.color = 'rgba(255,255,255,0.72)';

        const box = document.getElementById('msgbox-box');
        if (t.boxDark) {
            box.style.background = 'linear-gradient(155deg, rgba(30,32,40,0.96), rgba(15,16,22,0.93))';
            box.style.boxShadow = '0 18px 45px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.05)';
            box.style.border = '1px solid rgba(255,255,255,0.08)';
        } else {
            box.style.background = 'linear-gradient(155deg, rgba(255,255,255,0.96), rgba(248,250,252,0.91))';
            box.style.boxShadow = '0 18px 45px rgba(0,0,0,0.18)';
            box.style.border = '1px solid rgba(255,255,255,0.55)';
        }

        applyMsgStyle();
        renderBubbles();
    }

    // CUSTOM COLOUR THEME – derive gradient from single hex
    function applyCustomColor(hex) {
        const [h, s, l] = hexToHsl(hex);
        const c1 = hslToHex(h, s, Math.max(6, l - 34));
        const c2 = hex;
        const c3 = hslToHex(h, Math.max(20, s - 10), Math.min(90, l + 30));
        const boxDark = l < 35;
        const customTheme = { id:'custom', name:'Custom', c:[c1, c2, c3], deg:135, boxDark };
        document.querySelectorAll('.th-sw').forEach(s => s.classList.remove('on'));
        applyTheme(customTheme);
    }

    function hexToHsl(hex) {
        let r = parseInt(hex.slice(1,3),16)/255, g = parseInt(hex.slice(3,5),16)/255, b = parseInt(hex.slice(5,7),16)/255;
        const max = Math.max(r,g,b), min = Math.min(r,g,b);
        let h, s, l = (max+min)/2;
        if (max === min) { h = s = 0; }
        else {
            const d = max - min;
            s = l > 0.5 ? d/(2-max-min) : d/(max+min);
            switch (max) {
                case r: h = (g-b)/d + (g<b?6:0); break;
                case g: h = (b-r)/d + 2; break;
                case b: h = (r-g)/d + 4; break;
            }
            h *= 60;
        }
        return [h, s*100, l*100];
    }

    function hslToHex(h, s, l) {
        s /= 100; l /= 100;
        const k = n => (n + h/30) % 12;
        const a = s * Math.min(l, 1-l);
        const f = n => l - a * Math.max(-1, Math.min(k(n)-3, Math.min(9-k(n), 1)));
        const toHex = x => Math.round(255*x).toString(16).padStart(2,'0');
        return `#${toHex(f(0))}${toHex(f(8))}${toHex(f(4))}`;
    }

    // DECOR BUBBLES – fixed spots scaled to card
    const BUBBLE_SPOTS = [
        { left: 4, top: 20, r: 0.09 },
        { left: 94, top: 15, r: 0.13 },
        { left: 82, top: 5,  r: 0.045 },
        { left: 97, top: 86, r: 0.15 },
        { left: 18, top: 94, r: 0.11 },
    ];
    function renderBubbles() {
        const c = document.getElementById('card-bubbles');
        c.innerHTML = '';
        BUBBLE_SPOTS.forEach(b => {
            const d = document.createElement('div');
            const sz = Math.max(20, S.cW * b.r);
            d.className = 'card-bubble';
            d.style.width = sz + 'px';
            d.style.height = sz + 'px';
            d.style.left = `calc(${b.left}% - ${sz/2}px)`;
            d.style.top = `calc(${b.top}% - ${sz/2}px)`;
            c.appendChild(d);
        });
    }

    // UI BUILDERS – font list and colour palette
    function buildFontList(cid, active, cb) {
        const c = document.getElementById(cid);
        c.innerHTML = '';
        FONTS.forEach(f => {
            const el = document.createElement('div');
            el.className = 'fn-item' + (f.id===active?' on':'');
            el.innerHTML = `<span class="fn-preview" style="font-family:${f.id}">${f.preview}</span><span class="fn-name">${f.lbl}</span>`;
            el.addEventListener('click', () => {
                c.querySelectorAll('.fn-item').forEach(i=>i.classList.remove('on'));
                el.classList.add('on');
                cb(f.id);
            });
            c.appendChild(el);
        });
    }

    function buildColorRow(cid, activeCol, cb) {
        const c = document.getElementById(cid);
        c.innerHTML = '';
        COLORS.forEach(col => {
            const d = document.createElement('div');
            d.className = 'cl-dot' + (col===activeCol?' on':'') + (col==='#171725'?' dark':'');
            d.style.background = col;
            d.title = col;
            d.addEventListener('click', () => {
                c.querySelectorAll('.cl-dot').forEach(i=>i.classList.remove('on'));
                d.classList.add('on');
                cb(col);
            });
            c.appendChild(d);
        });
        const picker = document.createElement('input');
        picker.type = 'color'; picker.value = activeCol || '#ffffff'; picker.title = 'Custom';
        picker.addEventListener('input', () => { c.querySelectorAll('.cl-dot').forEach(i=>i.classList.remove('on')); cb(picker.value); });
        c.appendChild(picker);
    }

    // EXPORT – html2canvas render at higher resolution
    async function downloadCard() {
        const btn = document.getElementById('download-btn');
        btn.classList.add('busy');
        btn.textContent = '⏳ Rendering...';

        const selectedLayers = document.querySelectorAll('.sel');
        selectedLayers.forEach(el => el.classList.remove('sel'));
        document.querySelectorAll('.rot-handle').forEach(h => h.style.display = 'none');

        try {
            await document.fonts.ready;
            await new Promise(r => setTimeout(r, 300));
        } catch (e) {
            console.warn('Font loading issue:', e);
        }

        const card = document.getElementById('card');
        const targetW = 1080;
        const targetH = S.fmt === '9:16' ? 1920 : S.fmt === '4:5' ? 1350 : 1080;
        const scale = targetW / S.cW;

        try {
            const canvas = await html2canvas(card, {
                scale: scale,
                useCORS: true,
                allowTaint: true,
                backgroundColor: S.theme.c[0],
                width: S.cW,
                height: S.cH,
                logging: false,
                imageTimeout: 15000,
                scrollX: 0,
                scrollY: 0,
                windowWidth: document.documentElement.offsetWidth,
                windowHeight: document.documentElement.offsetHeight,
                onclone: (doc) => {
                    const clone = doc.getElementById('card');
                    if (clone) {
                        clone.style.boxShadow = 'none';
                        clone.style.transform = 'none';
                    }
                }
            });

            canvas.toBlob((blob) => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.download = `SecretGate-${S.fmt.replace(':','-')}.png`;  // Add your site name here
                a.href = url;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(() => URL.revokeObjectURL(url), 1000);
                toast('✓ Saved to your downloads!');
            }, 'image/png', 1.0);

        } catch(e) {
            console.error('Download error:', e);
            toast('❌ Download failed — please try again');
        }

        document.querySelectorAll('.rot-handle').forEach(h => h.style.display = '');
        selectedLayers.forEach(el => el.classList.add('sel'));
        btn.classList.remove('busy');
        btn.textContent = '⬇ Save Card';
    }

    // TOAST – transient status message
    let toastTimer = null;
    function toast(msg, d=2600) {
        const el = document.getElementById('toast');
        el.textContent = msg;
        el.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.classList.remove('show'), d);
    }

    // BOOT
    document.addEventListener('DOMContentLoaded', init);

    // LOADER HIDE – unmount loading overlay after fonts settle
    window.addEventListener('load', function () {
        setTimeout(function () {
            const loader = document.getElementById('loader-wrapper');
            if (loader) loader.classList.add('hidden');
        }, 1000);
    });

// =====================================================================
// CONGRATULATIONS, YOU HAVE REACHED THE END:)
// =====================================================================
</script>
</body>
</html>