<?php
// MITHOOOS - Database Migration Script (Turso SQLite)
// Usage: php backend/migrate.php

$envFile = dirname(__DIR__, 2) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 2);
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Database.php';

try {
    $db = Database::getInstance();
    
    echo "Connected to Turso database: " . getenv('TURSO_DATABASE_URL') . "\n";
    
    // Create migrations table if not exists
    $db->execute("
        CREATE TABLE IF NOT EXISTS schema_migrations (
            version VARCHAR(255) PRIMARY KEY,
            applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Get applied migrations
    $appliedRows = $db->fetchAll("SELECT version FROM schema_migrations ORDER BY version ASC");
    $applied = array_column($appliedRows, 'version');
    
    $migrationsDir = __DIR__ . '/config/migrations';
    $files = glob($migrationsDir . '/*.sql');
    sort($files);
    
    $executedCount = 0;
    
    foreach ($files as $file) {
        $version = basename($file, '.sql');
        
        if (!in_array($version, $applied)) {
            echo "Applying migration: $version...\n";
            $sql = file_get_contents($file);
            
            try {
                // Execute all queries in one go
                $queries = array_filter(array_map('trim', explode(';', $sql)));
                $requests = [];
                $requests[] = ['type' => 'execute', 'stmt' => ['sql' => 'BEGIN']];
                foreach ($queries as $q) {
                    if (empty($q)) continue;
                    $requests[] = [
                        'type' => 'execute',
                        'stmt' => ['sql' => $q]
                    ];
                }
                
                // Add the migration record insert
                $requests[] = [
                    'type' => 'execute',
                    'stmt' => [
                        'sql' => "INSERT INTO schema_migrations (version) VALUES (?)",
                        'args' => [['type' => 'text', 'value' => (string)$version]]
                    ]
                ];
                $requests[] = ['type' => 'execute', 'stmt' => ['sql' => 'COMMIT']];
                
                // Send single pipeline request
                $payload = ['requests' => $requests];
                
                // We use private sendRequest method using reflection or just use raw curl since Database doesn't expose it
                // Actually, let's just make a dedicated method in Database.php or do raw curl here:
                
                $ch = curl_init(getenv('TURSO_DATABASE_URL') . '/v2/pipeline');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . getenv('TURSO_AUTH_TOKEN'),
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode >= 400 || !$response) {
                    throw new Exception("Turso Schema Execution Error ($httpCode): $response");
                }
                
                echo "Migration $version applied successfully.\n";
                $executedCount++;
            } catch (Exception $e) {
                echo "ERROR applying $version: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
    }
    
    if ($executedCount === 0) {
        echo "No new migrations to apply.\n";
    }
    
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
    exit(1);
}
