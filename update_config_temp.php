<?php
// update_config_temp.php

$configFile = __DIR__ . '/config/config.php';
if (file_exists($configFile)) {
    $content = file_get_contents($configFile);
    if (strpos($content, 'API_SECRET_KEY') === false) {
        // Append definition
        $content .= "\n\n// Added automatically by deploy helper\ndefine('API_SECRET_KEY', 'ntkntk_secure_api_key_8b9f1a2c3d4e5f6a');\n";
        if (file_put_contents($configFile, $content)) {
            echo "SUCCESS: Successfully updated config.php with API_SECRET_KEY!";
        } else {
            echo "ERROR: Failed to write to config.php. Please check file permissions.";
        }
    } else {
        echo "SUCCESS: API_SECRET_KEY already exists in config.php.";
    }
} else {
    echo "ERROR: config.php not found at $configFile";
}
unlink(__FILE__); // Tự động xóa chính mình sau khi chạy để đảm bảo bảo mật tuyệt đối!
