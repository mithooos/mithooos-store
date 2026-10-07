<?php
/**
 * Cron Job: Cleanup Old Payment Screenshots
 * Deletes screenshots older than 7 days to manage storage and privacy.
 * 
 * Usage:
 * Run this script via a server cron job (e.g., daily at midnight).
 * Command: php /path/to/mithooos/backend/cron/cleanup_screenshots.php
 */

require __DIR__ . '/../config/constants.php';

$uploadDir = UPLOAD_DIR . '/payments';

if (!is_dir($uploadDir)) {
    echo "Upload directory does not exist. Nothing to clean.\n";
    exit(0);
}

$files = glob($uploadDir . '/*');
$now = time();
$deleted = 0;

foreach ($files as $file) {
    if (is_file($file)) {
        // Check if the file is older than 7 days (7 * 24 * 60 * 60 seconds)
        if ($now - filemtime($file) >= 7 * 24 * 60 * 60) {
            if (unlink($file)) {
                $deleted++;
                echo "Deleted: " . basename($file) . "\n";
            } else {
                echo "Failed to delete: " . basename($file) . "\n";
            }
        }
    }
}

echo "Cleanup complete. Total files deleted: $deleted\n";
