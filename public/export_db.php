<?php

// Export database
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

$host = Config::get('database.connections.mysql.host');
$db = Config::get('database.connections.mysql.database');
$user = Config::get('database.connections.mysql.username');
$pass = Config::get('database.connections.mysql.password');

$filename = "db_export_" . time() . ".sql";
$command = "mysqldump -h {$host} -u {$user} -p'{$pass}' {$db} > {$filename}";
exec($command);

if (file_exists($filename)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    unlink($filename);
    unlink(__FILE__); // self delete
    exit;
} else {
    echo "Failed to export.";
    unlink(__FILE__); // self delete
}
