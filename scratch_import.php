<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

function importSql($file) {
    echo "Importing $file...\n";
    if (!file_exists($file)) {
        echo "File $file not found!\n";
        return;
    }
    $sql = file_get_contents($file);
    try {
        DB::unprepared($sql);
        echo "Imported $file successfully.\n";
    } catch (\Exception $e) {
        echo "Error importing $file: " . $e->getMessage() . "\n";
    }
}

try {
    importSql('shop.sql');
    importSql('public/demo.sql');

    echo "Final Product Count: " . DB::table('products')->count() . "\n";
    
    echo "Extracting uploads.zip...\n";
    $zip = new ZipArchive;
    if ($zip->open('public/uploads.zip') === TRUE) {
        $zip->extractTo('public/uploads/all/');
        $zip->close();
        echo "Done extracting uploads.zip\n";
    } else {
        echo "Failed to open uploads.zip\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
