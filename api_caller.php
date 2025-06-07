<?php
function call_ai_api($prompt_text, $config) {
    $apiKey = $config['api_key'] ?? '';
    $apiEndpoint = $config['api_endpoint'] ?? '';
    $model = $config['model'] ?? 'gpt-3.5-turbo';
    $systemPrompt = $config['system_prompt'] ?? 'You are a helpful assistant.';

    if (empty($apiKey)) {
        return ['error' => 'API Key is not configured. Please check your settings.'];
    }
    if (empty($apiEndpoint)) {
        return ['error' => 'API Endpoint is not configured. Please check your settings.'];
    }

    $data = [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $prompt_text]
        ],
        'temperature' => 0.7,
        'max_tokens' => 2048,
    ];

    // Encode the data to a JSON string.
    // JSON_UNESCAPED_SLASHES and JSON_UNESCAPED_UNICODE are good practices.
    $jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // --- START: Professional HTTP Headers Upgrade ---
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
        'Accept: application/json', // Explicitly state we accept JSON in response.
        'User-Agent: LiteAI-Writer/1.0', // Add a user agent to identify our client.
        'Content-Length: ' . strlen($jsonData) // Manually calculate and set Content-Length. This is crucial.
    ];
    // --- END: Professional HTTP Headers Upgrade ---

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    
    // Set the custom headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Timeout settings
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); // Connection timeout
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);      // Total execution timeout for the request

    // SSL Certificate verification (from previous fix)
    // Assuming php.ini is configured correctly, so we don't disable verification.
    // If you still have SSL issues, you might need to specify the CA path here as a fallback.
    // curl_setopt($ch, CURLOPT_CAINFO, 'D:/path/to/your/cacert.pem');

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // --- Enhanced Error Handling (unchanged from previous version) ---
    if ($curl_error) {
       return ['error' => "cURL Error: " . $curl_error];
    }

    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => "Failed to decode API response. It might not be valid JSON. HTTP Status: {$httpcode}. Response body: " . substr(strip_tags($response), 0, 500)
        ];
    }
    
    if (isset($result['error']) && is_array($result['error'])) {
        $error_message = $result['error']['message'] ?? 'Unknown API error.';
        $error_type = $result['error']['type'] ?? 'unknown_type';
        $error_code = $result['error']['code'] ?? 'unknown_code';

        // Friendly message for internal server errors
        if ($error_code === 'internal_server_error' || $httpcode >= 500) {
            return ['error' => "API Error: The AI provider's server encountered a temporary problem ({$error_code}). This is often intermittent. Please try again in a moment. Request ID: " . ($result['error']['request_id'] ?? 'N/A')];
        }

        if ($error_code === 'invalid_api_key') {
            return ['error' => "Invalid API Key. Please check your key in the settings. [API Type: {$error_type}]"];
        }
        if ($error_code === 'insufficient_quota') {
             return ['error' => "You have exceeded your current quota. Please check your plan and billing details. [API Type: {$error_type}]"];
        }

        return ['error' => "API Error: {$error_message} (Type: {$error_type}, Code: {$error_code})"];
    }

    if ($httpcode !== 200) {
        return [
            'error' => "API request failed with HTTP status code: {$httpcode}. Response: " . substr($response, 0, 500)
        ];
    }

    $content = $result['choices'][0]['message']['content'] ?? null;
    if ($content !== null) {
        return ['success' => $content];
    }

    return [
        'error' => 'Unexpected API response format. The structure of the successful response was not recognized. Full Response: ' . json_encode($result, JSON_PRETTY_PRINT)
    ];
}
?>
