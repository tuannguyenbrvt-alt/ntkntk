<?php
// migrate.php
require_once 'config/config.php';
require_once 'config/database.php';

header('Content-Type: text/plain; charset=utf-8');

// Simple key check for basic security (optional, but good practice)
$key = $_GET['key'] ?? '';
if ($key !== '8b9f1a2c3d4e5f6a') {
    die("Error: Unauthorized access.");
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if column already exists
    $check = $db->query("SHOW COLUMNS FROM question_bank LIKE 'question_type'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE question_bank ADD COLUMN question_type ENUM('single', 'multiple') NOT NULL DEFAULT 'single' AFTER question_text");
        echo "Migration Success: 'question_type' column added to question_bank table.\n";
    } else {
        echo "Migration Skipped: 'question_type' column already exists in question_bank table.\n";
    }
} catch (Exception $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
}
