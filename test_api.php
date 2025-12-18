<?php

require 'vendor/autoload.php';

use MeinVerband\Api\MeinVerbandApi;
use MeinVerband\Api\ApiException;

echo "MeinVerband API Test\n";
echo "===================\n\n";

try {
    // API-Client mit .env.local Konfiguration initialisieren
    $api = new MeinVerbandApi();
    echo "✓ API-Client initialisiert\n";
    
    // Test: Vereine abrufen
    echo "\n1. Teste Vereine abrufen...\n";
    $vereine = $api->verein()->getAll();
    echo "   Response: " . json_encode($vereine, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    // Test: Mitglieder abrufen
    echo "\n2. Teste Mitglieder abrufen...\n";
    $mitglieder = $api->mitglied()->getAll();
    echo "   Response: " . json_encode($mitglieder, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    echo "\n✓ API-Tests abgeschlossen!\n";
    
} catch (ApiException $e) {
    echo "❌ API-Fehler: " . $e->getMessage() . "\n";
    echo "   Details: " . $e->getTraceAsString() . "\n";
} catch (Exception $e) {
    echo "❌ Allgemeiner Fehler: " . $e->getMessage() . "\n";
}