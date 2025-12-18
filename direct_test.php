<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use MeinVerband\Api\Config;

echo "Direct API Test\n";
echo "===============\n\n";

// Lade Konfiguration aus .env
$config = new Config();

$client = new Client();

$postData = [
    'api_action' => 'list,mitglied_nur_funktionenaere',
    'api_user' => $config->getApiUser(),
    'api_pwd' => $config->getApiPassword()
];

echo "Sending POST request to: " . $config->getApiUrl() . "\n";
echo "POST data: " . json_encode(['api_request' => json_encode($postData)], JSON_UNESCAPED_UNICODE) . "\n\n";

try {
    $response = $client->post($config->getApiUrl(), [
        'form_params' => [
            'api_request' => json_encode($postData)
        ],
        'headers' => [
            'User-Agent' => 'MeinVerband-API-Client/1.0'
        ]
    ]);
    
    $body = $response->getBody()->getContents();
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Body: " . $body . "\n";
    
    $json = json_decode($body, true);
    if ($json) {
        echo "Parsed JSON: " . json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}