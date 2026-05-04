<?php
$endpoints = [
    'products' => 'http://active-ecommerce.test/api/v3/products',
    'categories' => 'http://active-ecommerce.test/api/v3/categories',
    'brands' => 'http://active-ecommerce.test/api/v3/brands',
    'settings' => 'http://active-ecommerce.test/api/v3/settings',
];

foreach ($endpoints as $name => $url) {
    echo "Testing $name...\n";
    $response = @file_get_contents($url);
    if ($response === false) {
        echo "FAILED: $url\n";
        continue;
    }
    
    $json = json_decode($response, true);
    if (isset($json['success']) && $json['success'] === true) {
        echo "OK: $name (Returned " . (isset($json['data']) ? count($json['data']) : '0') . " items/keys)\n";
    } else {
        echo "ERROR FORMAT: " . substr($response, 0, 200) . "\n";
    }
}
