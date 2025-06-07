<?php
define('CONFIG_FILE', __DIR__ . '/data/config.json');

function load_config() {
    if (!file_exists(CONFIG_FILE)) {
        // Provide default structure if file doesn't exist
        return [
            'api_key' => '',
            'api_endpoint' => 'https://api.openai.com/v1/chat/completions', // Default example
            'model' => 'gpt-3.5-turbo',
            'system_prompt' => 'You are a helpful assistant.'
        ];
    }
    $json = file_get_contents(CONFIG_FILE);
    return json_decode($json, true) ?: []; // Return empty array on decode error
}

function save_config($config) {
    // Basic validation (can be improved)
    if (!isset($config['api_key']) || !isset($config['api_endpoint']) || !isset($config['model'])) {
       return false;
    }
     // Ensure data directory exists and is writable
    $dataDir = dirname(CONFIG_FILE);
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true); // Try to create it
    }
     if (!is_writable($dataDir)) {
        // Handle error appropriately in a real app (e.g., throw exception, return specific error)
        error_log("Error: Data directory '{$dataDir}' is not writable.");
        return false;
    }

    // Check if the file itself is writable (or can be created)
    if (file_exists(CONFIG_FILE) && !is_writable(CONFIG_FILE)) {
         error_log("Error: Config file '".CONFIG_FILE."' is not writable.");
         return false;
    }


    $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (file_put_contents(CONFIG_FILE, $json) === false) {
         error_log("Error: Failed to write to config file '".CONFIG_FILE."'. Check permissions.");
         return false;
    }
    return true;
}
?>
