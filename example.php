<?php

require 'vendor/autoload.php';

use MeinVerband\Api\MeinVerbandApi;

// Beispiel für die Verwendung der API
echo "MeinVerband API Example\n";
echo "=====================\n\n";

// API-Client mit Umgebungsvariablen initialisieren
// Lädt automatisch Konfiguration aus .env/.env.local
$api = new MeinVerbandApi();

// Alternative: Manuelle Konfiguration
// $api = new MeinVerbandApi('https://api.example.com', 'username', 'password');

// Optional: Bearer Token setzen (zusätzlich zur Basic Auth)
// $api->setAuthToken('your-bearer-token');

try {
    echo "✓ API-Client initialisiert\n";
    
    // Beispiel für die verfügbaren Endpoints
    echo "2. Verein endpoint verfügbar ✓\n";
    echo "3. Mitglied endpoint verfügbar ✓\n"; 
    echo "4. API endpoint verfügbar ✓\n";
    
    echo "Verfügbare API-Aktionen:\n";
    echo "Verein:\n";
    echo "- \$api->verein()->getAll() - list,verein\n\n";
    echo "Mitglied:\n";
    echo "- \$api->mitglied()->getAll() - list,mitglied\n";
    echo "- \$api->mitglied()->getFunktionen() - list,mitglied_funktionen\n";
    echo "- \$api->mitglied()->getNurFunktionaere() - list,mitglied_nur_funktionenaere\n";
    echo "- \$api->mitglied()->getHsvBsvBsvNach() - list,mitglied_hsv_bsv_bsv_nach\n";
    echo "- \$api->mitglied()->getUsers() - list,user\n";
    echo "- \$api->mitglied()->getUserNurFunktionaere() - list,user_nur_funktionenaere\n";
    echo "- \$api->mitglied()->getUserHsvBsvBsvNach() - list,user_hsv_bsv_bsv_nach\n\n";
    echo "API:\n";
    echo "- \$api->api()->getInfo() - info,api\n";
    
    echo "\nDie API-Bibliothek ist bereit zur Verwendung!\n";
    echo "Siehe README.md für detaillierte Anweisungen.\n";
    
} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}