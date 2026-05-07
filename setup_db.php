<?php
/**
 * Luxit Global Escapes - Database Setup Utility
 * This script creates the database if it doesn't exist and imports the schema.
 */

// 1. Manually load .env since constants aren't defined yet
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die("Error: .env file not found. Please create it first.\n");
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$config = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($name, $value) = explode('=', $line, 2);
    $config[trim($name)] = trim($value);
}

$host = $config['DB_HOST'] ?? 'localhost';
$dbname = $config['DB_NAME'] ?? 'luxit_global';
$user = $config['DB_USER'] ?? 'root';
$pass = $config['DB_PASS'] ?? '';

try {
    // 2. Connect to MySQL without specifying a database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Checking database '$dbname'...\n";

    // 3. Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$dbname' created or already exists.\n";

    // 4. Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 5. Import SQL schema
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("Error: database.sql not found at $sqlFile\n");
    }

    echo "Importing schema from database.sql...\n";
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    // This is a simple split, might fail on complex SQL, but good for our current file
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }

    echo "\nSuccess! Database '$dbname' is ready for use.\n";
    echo "You can now login to the admin panel with:\n";
    echo "Username: admin\nPassword: password\n";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage() . "\n");
}
