<?php
// Secure key to prevent unauthorized execution
$isCli = (php_sapi_name() === 'cli');
if (!$isCli && (!isset($_GET['key']) || $_GET['key'] !== 'rudra_deploy_2026')) {
    die("Unauthorized access.\n");
}

$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die(".env file not found.\n");
}

// Parse .env manually (more robust than parse_ini_file)
$env = [];
if ($lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            $val = trim($val, '"\'');
            $env[$key] = $val;
        }
    }
}

$host = $env['DB_HOST'] ?? 'localhost';
$db = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

if (empty($db)) {
    die("Database details not configured in .env.\n");
}

function out($msg) {
    global $isCli;
    if ($isCli) {
        $msg = str_replace(['<h3>', '</h3>', '<strong>', '</strong>', '<br>', '<br/>'], ['', "\n", '', '', "\n", "\n"], $msg);
        echo $msg;
    } else {
        echo $msg;
    }
}

out("<h3>Database Import Utility</h3>");
out("Connecting to database: $db on $host...<br>");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    out("Connected successfully!<br>");
    out("Dropping all existing tables...<br>");

    // Disable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE `$table`");
        out("Dropped table: $table<br>");
    }

    out("All tables dropped successfully!<br>");

    // File to import
    $sqlFile = __DIR__ . '/../database_backup.sql';
    if (!file_exists($sqlFile)) {
        die("ERROR: database_backup.sql file not found.\n");
    }

    out("Importing database_backup.sql (" . number_format(filesize($sqlFile)) . " bytes)...<br>");

    // Read and execute SQL file
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    out("<strong>SUCCESS: Database successfully imported!</strong><br>");

    // Enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Delete the script for security
    unlink(__FILE__);
    out("This script (import_database.php) has deleted itself for security.<br>");

} catch (Exception $e) {
    out("ERROR: " . $e->getMessage() . "<br>");
}
