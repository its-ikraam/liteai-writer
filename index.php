<?php
// --- START: Language and Session Initialization ---
session_start();

$supported_langs = ['en', 'zh-CN', 'zh-TW'];
$lang_files = [
    'en' => __DIR__ . '/lang/en.php',
    'zh-CN' => __DIR__ . '/lang/zh-CN.php',
    'zh-TW' => __DIR__ . '/lang/zh-TW.php',
];

$current_lang = 'en'; // Default

if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_langs)) {
    $current_lang = $_GET['lang'];
    $_SESSION['lang'] = $current_lang;
} elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $supported_langs)) {
    $current_lang = $_SESSION['lang'];
} elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $browser_lang_full = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5); // e.g., en-US, zh-CN
    $browser_lang_short = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // e.g., en, zh
    
    if (in_array($browser_lang_full, $supported_langs)) {
        $current_lang = $browser_lang_full;
    } elseif ($browser_lang_full === 'zh-TW' || $browser_lang_full === 'zh-HK') {
        $current_lang = 'zh-TW';
    } elseif ($browser_lang_short === 'zh') {
        $current_lang = 'zh-CN';
    } elseif ($browser_lang_short === 'en') {
        $current_lang = 'en';
    }
}

$lang_text = [];
if (file_exists($lang_files[$current_lang])) {
    $lang_text = require $lang_files[$current_lang];
} else {
    $lang_text = require $lang_files['en'];
}

function t($key, $default = '') {
    global $lang_text;
    return $lang_text[$key] ?? $default;
}
// --- END: Language and Session Initialization ---


// --- Backend Logic (same as before) ---
require_once 'config.php';
require_once 'prompts.php';
require_once 'api_caller.php';
try { $config = load_config(); $prompts = load_prompts(); } catch (Exception $e) { die("Error loading core data: " . $e->getMessage()); }
$all_prompts = array_merge($prompts['built_in'] ?? [], $prompts['user_defined'] ?? []);
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$error_message = $_SESSION['error_message'] ?? null; $success_message = $_SESSION['success_message'] ?? null; $generated_text = $_SESSION['generated_text'] ?? null; $user_input_persist = $_SESSION['user_input'] ?? '';
unset($_SESSION['error_message'], $_SESSION['success_message'], $_SESSION['generated_text'], $_SESSION['user_input']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($action) {
            case 'generate':
                $user_input = trim($_POST['user_input'] ?? '');
                $selected_prompt_name = $_POST['prompt_name'] ?? null;
                if (empty($user_input)) throw new Exception("Input text cannot be empty.");
                if (empty($selected_prompt_name)) throw new Exception("Please select a prompt.");
                $prompt_template = '';
                foreach ($all_prompts as $p) { if ($p['name'] === $selected_prompt_name) { $prompt_template = $p['prompt']; break; } }
                if (empty($prompt_template)) throw new Exception("Selected prompt not found.");
                $final_prompt_text = str_replace('{user_input}', $user_input, $prompt_template);
                $result = call_ai_api($final_prompt_text, $config);
                if (isset($result['success'])) { $_SESSION['generated_text'] = $result['success']; $_SESSION['success_message'] = "Text generated!"; } 
                else { throw new Exception($result['error'] ?? 'Unknown API error.'); }
                break;
            case 'save_config':
                $new_config = ['api_key' => $_POST['api_key'] ?? '', 'api_endpoint' => $_POST['api_endpoint'] ?? '', 'model' => $_POST['model'] ?? '', 'system_prompt' => $_POST['system_prompt'] ?? ''];
                if (!save_config($new_config)) { throw new Exception("Failed to save configuration."); }
                $_SESSION['success_message'] = "Configuration saved.";
                break;
            case 'add_prompt':
                $name = trim($_POST['new_prompt_name'] ?? ''); $text = trim($_POST['new_prompt_text'] ?? '');
                if (empty($name) || empty($text)) throw new Exception("Prompt name and text cannot be empty.");
                if (strpos($text, '{user_input}') === false) throw new Exception("Prompt text must include {user_input}.");
                if (!add_user_prompt($name, $text)) { throw new Exception("Failed to add prompt."); }
                $_SESSION['success_message'] = "Prompt '{$name}' added.";
                break;
            case 'delete_prompt':
                 $name = $_POST['prompt_name_to_delete'] ?? '';
                 if (empty($name)) throw new Exception("No prompt specified for deletion.");
                 if (!delete_user_prompt($name)) { throw new Exception("Failed to delete prompt '{$name}'."); }
                 $_SESSION['success_message'] = "Prompt '{$name}' deleted.";
                 break;
        }
        if (isset($_SESSION['error_message'])) { $_SESSION['user_input'] = $_POST['user_input'] ?? ''; }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        $_SESSION['user_input'] = $_POST['user_input'] ?? $user_input_persist;
    }
    header("Location: " . $_SERVER['PHP_SELF']); exit;
}
// --- SVG Assets (same as before) ---
$svg_mascot = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g transform="translate(50 50)"><circle r="45" fill="#e0f7fa"/><path d="M -25 -15 Q 0 -25 25 -15" stroke="#4dd0e1" stroke-width="4" fill="none" stroke-linecap="round"/><circle cx="-15" cy="5" r="5" fill="#00796b"/><circle cx="15" cy="5" r="5" fill="#00796b"/><path d="M 0 10 Q 5 20 10 10" stroke="#00796b" stroke-width="3" fill="none" stroke-linecap="round"/></g></svg>';
$svg_sparkle = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0a1.5 1.5 0 0 1 1.5 1.5V3h-3V1.5A1.5 1.5 0 0 1 8 0zM8 13a1.5 1.5 0 0 1-1.5 1.5V16h3v-1.5A1.5 1.5 0 0 1 8 13zM1.5 6.5A1.5 1.5 0 0 1 0 8v3h1.5V8a1.5 1.5 0 0 1 1.5-1.5h.086L4.5 9.414l-1.06-1.06a.5.5 0 0 1 0-.708l1.06-1.06-2.414-2.415a.5.5 0 0 1-.708 0L.586 5.586l-.086-.086H1.5zM14.5 6.5a1.5 1.5 0 0 1 1.5 1.5v3h-1.5V8a1.5 1.5 0 0 1-1.5-1.5h-.086l-1.414 1.414 1.06 1.06a.5.5 0 0 1 0 .708l-1.06 1.06 2.414 2.415a.5.5 0 0 1 .708 0l1.06-1.06.086.086H14.5zM8 4.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/></svg>';
$svg_settings = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311a1.464 1.464 0 0 1-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413-1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.858 2.929 2.929 0 0 1 0 5.858z"/></svg>';
$svg_list = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.317 1.494l-.545 2.179c-.229.916-.926 1.638-1.857 1.816-1.28.248-2.522-.656-2.768-1.932L1.08 4.21c-.26-.82.11-1.664.773-2.16l1.03-1.03zM2.5 1.666a.5.5 0 0 0-.354.147L1.11 2.85c-.333.333-.451.81-.302 1.256l.39 1.556c.14.56.66.974 1.232.974.353 0 .7-.145.943-.418l.54-2.162c.11-.44-.004-.91-.285-1.248L2.854 1.814a.5.5 0 0 0-.354-.148z"/></svg>';
$svg_plus = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>';
$svg_trash = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>';
$svg_copy = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h-1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1V5H2z"/></svg>';
$svg_save = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v4.5h2a.5.5 0 0 1 .354.854l-2.5 2.5a.5.5 0 0 1-.708 0l-2.5-2.5A.5.5 0 0 1 5.5 6.5h2V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/></svg>';
$svg_background = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" height="80"><circle fill="%23f0f4f8" cx="40" cy="40" r="2" /></svg>';
?>
<!DOCTYPE html>
<html lang="<?php echo str_replace('_', '-', $current_lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('page_title'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* All the cute CSS from the previous version goes here. No changes needed. */
        :root { --c-bg: #fdfcfa; --c-text: #5a5a5a; --c-primary: #82a0d8; --c-primary-light: #dbe4ff; --c-secondary: #ffc8dd; --c-accent: #a0d8b3; --c-white: #ffffff; --c-border: #eeeeee; --c-shadow: rgba(130, 160, 216, 0.15); --c-success-bg: #e6f7e9; --c-success-text: #4caf50; --c-error-bg: #ffebee; --c-error-text: #f44336; --font-main: 'M PLUS Rounded 1c', sans-serif; --border-radius: 16px; --transition-smooth: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); }
        body { font-family: var(--font-main); background-color: var(--c-bg); background-image: url("<?php echo $svg_background; ?>"); color: var(--c-text); line-height: 1.7; padding: 2rem 1rem; max-width: 800px; margin: 0 auto; }
        h1, h2, h3 { font-weight: 700; color: var(--c-primary); }
        svg { vertical-align: middle; }
        .header { text-align: center; margin-bottom: 2.5rem; }
        .header .mascot { width: 80px; height: 80px; margin-bottom: 1rem; }
        .header h1 { font-size: 2.5rem; margin: 0; }
        .header p { font-size: 1.1rem; color: var(--c-text); }
        .card { background: var(--c-white); border-radius: var(--border-radius); padding: 2rem; margin-bottom: 2rem; box-shadow: 0 8px 25px var(--c-shadow); border: 1px solid var(--c-border); transition: var(--transition-smooth); }
        .card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px var(--c-shadow); }
        details > summary { font-size: 1.2rem; font-weight: 700; color: var(--c-primary); cursor: pointer; padding: 1rem; border-radius: var(--border-radius); transition: var(--transition-smooth); display: flex; align-items: center; gap: 0.75rem; list-style: none; }
        details > summary::before { content: '▶'; font-size: 0.8em; transition: transform 0.3s ease; transform-origin: center; }
        details[open] > summary::before { transform: rotate(90deg); }
        details > summary:hover { background: var(--c-primary-light); }
        details .card { margin-top: 1rem; box-shadow: none; border: 1px solid var(--c-border); }
        label { display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.9rem; color: #777; }
        select, textarea, input[type="text"], input[type="url"], input[type="password"] { width: 100%; padding: 0.8rem 1rem; border-radius: 12px; border: 2px solid var(--c-border); background: #f9f9f9; transition: var(--transition-smooth); font-family: var(--font-main); }
        select:focus, textarea:focus, input:focus { outline: none; border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-light); }
        textarea { height: 150px; resize: vertical; }
        button, .button { background-color: var(--c-primary); color: var(--c-white); border: none; padding: 0.9rem 1.5rem; border-radius: 50px; font-weight: 700; font-family: var(--font-main); cursor: pointer; transition: var(--transition-smooth); display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
        button:hover, .button:hover { transform: translateY(-3px) scale(1.03); box-shadow: 0 8px 15px var(--c-shadow); }
        button.secondary { background-color: var(--c-accent); }
        button.danger, .prompt-list button { background-color: var(--c-secondary); }
        .prompt-list button { padding: 0.5rem 0.8rem; font-size: 0.8em; }
        .result-box { background-color: var(--c-primary-light); border: 2px dashed var(--c-primary); border-radius: var(--border-radius); padding: 1.5rem; margin-top: 1.5rem; white-space: pre-wrap; word-wrap: break-word; min-height: 100px; position: relative; }
        .result-box .copy-button { position: absolute; top: 1rem; right: 1rem; }
        .message { padding: 1rem; margin-bottom: 1.5rem; border-radius: var(--border-radius); text-align: center; }
        .message.error { background: var(--c-error-bg); color: var(--c-error-text); }
        .message.success { background: var(--c-success-bg); color: var(--c-success-text); }
        .prompt-list ul { list-style: none; padding: 0; }
        .prompt-list li { display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-bottom: 1px solid var(--c-border); }
        .prompt-list li:last-child { border-bottom: none; }
        .prompt-list small { display: block; color: #999; font-size: 0.85em; }
        .loading-overlay { display: none; position: fixed; inset: 0; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(5px); z-index: 1000; justify-content: center; align-items: center; flex-direction: column; gap: 1rem; }
        .loading-overlay[aria-busy="true"] { display: flex; }
        .loading-overlay .mascot { width: 100px; height: 100px; animation: bounce 1.5s infinite ease-in-out; }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
        .loading-overlay p { font-size: 1.2rem; font-weight: 700; color: var(--c-primary); }
        footer { text-align: center; margin-top: 3rem; color: #aaa; font-size: 0.9rem; }
        .lang-switcher { position: absolute; top: 1.5rem; right: 1.5rem; z-index: 100; font-size: 0.9em; display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.7); padding: 0.3rem 0.8rem; border-radius: 50px; backdrop-filter: blur(2px); }
        .lang-switcher label { margin: 0; font-weight: normal; color: var(--c-text); }
        .lang-switcher select { width: auto; padding: 0.2rem 0.5rem; border-radius: 8px; border: 1px solid var(--c-border); background: transparent; -webkit-appearance: none; appearance: none; }
    </style>
</head>
<body>

    <div class="loading-overlay" id="loading-indicator" aria-busy="false">
        <div class="mascot"><?php echo $svg_mascot; ?></div>
        <p><?php echo t('loading_text'); ?></p>
    </div>

    <div class="lang-switcher">
        <label for="lang-switcher-select"><?php echo t('lang_switch_label'); ?></label>
        <select id="lang-switcher-select" onchange="window.location.href = '?lang=' + this.value;">
            <option value="en" <?php if($current_lang === 'en') echo 'selected'; ?>>English</option>
            <option value="zh-CN" <?php if($current_lang === 'zh-CN') echo 'selected'; ?>>简体中文</option>
            <option value="zh-TW" <?php if($current_lang === 'zh-TW') echo 'selected'; ?>>繁體中文</option>
        </select>
    </div>

    <div class="header">
        <div class="mascot"><?php echo $svg_mascot; ?></div>
        <h1><?php echo t('header_title'); ?></h1>
        <p><?php echo t('header_subtitle'); ?></p>
    </div>

    <main>
        <?php if ($error_message): ?><div class="message error"><?php echo t('error_message') . htmlspecialchars($error_message); ?></div><?php endif; ?>
        <?php if ($success_message): ?><div class="message success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>

        <form class="card" method="POST" action="index.php" id="generate-form">
            <input type="hidden" name="action" value="generate">
            
            <div style="margin-bottom: 1.5rem;">
                <label for="prompt_name"><?php echo t('prompt_label'); ?></label>
                <select id="prompt_name" name="prompt_name" required>
                    <option value="" disabled selected><?php echo t('prompt_placeholder'); ?></option>
                    <?php if(!empty($prompts['built_in'])): ?><optgroup label="<?php echo t('prompt_group_builtin'); ?>"><?php foreach ($prompts['built_in'] as $p): ?><option value="<?php echo htmlspecialchars($p['name']); ?>"><?php echo htmlspecialchars($p['name']); ?></option><?php endforeach; ?></optgroup><?php endif; ?>
                    <?php if(!empty($prompts['user_defined'])): ?><optgroup label="<?php echo t('prompt_group_user'); ?>"><?php foreach ($prompts['user_defined'] as $p): ?><option value="<?php echo htmlspecialchars($p['name']); ?>"><?php echo htmlspecialchars($p['name']); ?></option><?php endforeach; ?></optgroup><?php endif; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="user_input"><?php echo t('input_label'); ?></label>
                <textarea id="user_input" name="user_input" required placeholder="<?php echo t('input_placeholder'); ?>"><?php echo htmlspecialchars($user_input_persist); ?></textarea>
            </div>
            
            <button type="submit"><?php echo $svg_sparkle; ?> <?php echo t('button_generate'); ?></button>
        </form>

        <?php if ($generated_text): ?>
        <div class="card">
            <h2><?php echo t('result_card_title'); ?></h2>
            <div class="result-box">
                <?php echo nl2br(htmlspecialchars($generated_text)); ?>
                <button class="copy-button" id="copy-button" title="<?php echo t('copy_button_text'); ?>"><?php echo $svg_copy; ?></button>
            </div>
        </div>
        <?php endif; ?>

        <details>
            <summary><?php echo $svg_settings; ?> <?php echo t('settings_title'); ?></summary>
            <div class="card">
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="save_config">
                    <p style="font-size: 0.9em; color: #999; margin-top:0;"><?php echo t('settings_warning'); ?></p>
                    <div style="margin-bottom: 1rem;"><label for="api_key"><?php echo t('api_key_label'); ?></label><input type="password" id="api_key" name="api_key" value="<?php echo htmlspecialchars($config['api_key'] ?? ''); ?>" required></div>
                    <div style="margin-bottom: 1rem;"><label for="api_endpoint"><?php echo t('api_endpoint_label'); ?></label><input type="url" id="api_endpoint" name="api_endpoint" value="<?php echo htmlspecialchars($config['api_endpoint'] ?? ''); ?>" required></div>
                    <div style="margin-bottom: 1rem;"><label for="model"><?php echo t('model_name_label'); ?></label><input type="text" id="model" name="model" value="<?php echo htmlspecialchars($config['model'] ?? ''); ?>" required></div>
                    <div style="margin-bottom: 1.5rem;"><label for="system_prompt"><?php echo t('system_prompt_label'); ?></label><textarea id="system_prompt" name="system_prompt" placeholder="<?php echo t('system_prompt_placeholder'); ?>"><?php echo htmlspecialchars($config['system_prompt'] ?? ''); ?></textarea></div>
                    <button type="submit"><?php echo $svg_save; ?> <?php echo t('button_save_settings'); ?></button>
                </form>
            </div>
        </details>

        <details>
             <summary><?php echo $svg_list; ?> <?php echo t('prompt_manage_title'); ?></summary>
             <div class="card prompt-list">
                 <h3><?php echo $svg_plus; ?> <?php echo t('add_prompt_title'); ?></h3>
                 <form method="POST" action="index.php">
                     <input type="hidden" name="action" value="add_prompt">
                     <div style="margin-bottom: 1rem;"><label for="new_prompt_name"><?php echo t('prompt_name_label'); ?></label><input type="text" id="new_prompt_name" name="new_prompt_name" required></div>
                     <div style="margin-bottom: 1.5rem;"><label for="new_prompt_text"><?php echo t('prompt_formula_label'); ?></label><textarea id="new_prompt_text" name="new_prompt_text" required></textarea></div>
                     <button type="submit"><?php echo $svg_plus; ?> <?php echo t('button_add_prompt'); ?></button>
                 </form>
                 <hr style="margin: 2rem 0; border-top: 1px solid var(--c-border); border-bottom:0;">
                 <h3><?php echo t('my_prompts_title'); ?></h3>
                 <?php if (!empty($prompts['user_defined'])): ?>
                     <ul>
                         <?php foreach ($prompts['user_defined'] as $p): ?>
                         <li>
                             <div><strong><?php echo htmlspecialchars($p['name']); ?></strong><small><?php echo htmlspecialchars($p['prompt']); ?></small></div>
                             <form method="POST" action="index.php" onsubmit="return confirm('<?php echo t('delete_prompt_confirm'); ?>');">
                                 <input type="hidden" name="action" value="delete_prompt">
                                 <input type="hidden" name="prompt_name_to_delete" value="<?php echo htmlspecialchars($p['name']); ?>">
                                 <button type="submit" title="<?php echo t('delete_button_title'); ?>" class="danger"><?php echo $svg_trash; ?></button>
                             </form>
                         </li>
                         <?php endforeach; ?>
                     </ul>
                 <?php else: ?><p><?php echo t('no_prompts_text'); ?></p><?php endif; ?>
             </div>
        </details>
    </main>

    <footer>
        <p><?php echo t('footer_text'); ?></p>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const generateForm = document.getElementById('generate-form');
        const loadingIndicator = document.getElementById('loading-indicator');
        if (generateForm) {
            generateForm.addEventListener('submit', () => {
                if (document.getElementById('prompt_name').value && document.getElementById('user_input').value.trim()) {
                    loadingIndicator.setAttribute('aria-busy', 'true');
                }
            });
        }
        window.addEventListener('pageshow', () => loadingIndicator.setAttribute('aria-busy', 'false'));

        const copyButton = document.getElementById('copy-button');
        if (copyButton) {
            const originalTitle = '<?php echo t('copy_button_text'); ?>';
            const copiedTitle = '<?php echo t('copy_button_copied'); ?>';
            
            copyButton.addEventListener('click', () => {
                const resultBox = document.querySelector('.result-box');
                navigator.clipboard.writeText(resultBox.innerText.trim()).then(() => {
                    copyButton.innerHTML = '✅';
                    copyButton.title = copiedTitle;
                    setTimeout(() => {
                        copyButton.innerHTML = `<?php echo addslashes($svg_copy); ?>`;
                        copyButton.title = originalTitle;
                    }, 2000);
                });
            });
        }
    });
    </script>
</body>
</html>
