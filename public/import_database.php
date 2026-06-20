<?php
// Secure key to prevent unauthorized execution
if (!isset($_GET['key']) || $_GET['key'] !== 'rudra_deploy_2026') {
    die("Unauthorized access.");
}

$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die(".env file not found.");
}

// Parse .env
$env = parse_ini_file($envPath);
$host = $env['DB_HOST'] ?? 'localhost';
$db = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

if (empty($db)) {
    die("Database details not configured in .env.");
}

echo "<h3>Database Import Utility</h3>";
echo "Connecting to database: $db on $host...<br>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    echo "Connected successfully!<br>";
    echo "Dropping all existing tables...<br>";

    // Disable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE `$table`");
        echo "Dropped table: $table<br>";
    }

    echo "All tables dropped successfully!<br>";

    // File to import
    $sqlFile = __DIR__ . '/../database_backup.sql';
    if (!file_exists($sqlFile)) {
        die("ERROR: database_backup.sql file not found.");
    }

    echo "Importing database_backup.sql (" . number_format(filesize($sqlFile)) . " bytes)...<br>";

    // Read and execute SQL file
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    echo "<strong>SUCCESS: Database successfully imported!</strong><br>";

    // Enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Delete the script for security
    unlink(__FILE__);
    echo "This script (import_database.php) has deleted itself for security.<br>";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}
