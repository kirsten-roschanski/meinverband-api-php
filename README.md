# MeinVerband API PHP-Client

Eine PHP-Bibliothek für die Integration der MeinVerband API in Contao und WordPress Projekte.

## Über die VerbandsCloud

Die VerbandsCloud wird von der **syscape® GmbH** im Auftrag des **Deutschen Imkerbundes** entwickelt und betrieben. Diese API-Bibliothek ermöglicht die Integration von Vereins- und Mitgliederdaten aus der VerbandsCloud in externe Systeme.

**Beispiel-Konfiguration für Imkervereine:**
- Landesverband: Hessische Imker
- API-URL: `https://hessen.meinverband.online/api_server.php/api`

## Installation

Installieren Sie die Bibliothek über Composer:

```bash
composer require meinverband/api
```

## Verwendung

### Basis-Setup

```php
<?php

use MeinVerband\Api\MeinVerbandApi;

// 1. Konfiguration über .env/.env.local (empfohlen)
$api = new MeinVerbandApi();

// 2. Manuelle Konfiguration
$api = new MeinVerbandApi(
    'https://hessen.meinverband.online/api_server.php/api', 
    'verein-xxx', 
    'your_password'
);

// Optional: Bearer Token für zusätzliche Authentifizierung (falls benötigt)
// $api->setAuthToken('your-bearer-token');
```

## Konfiguration

### Umgebungsvariablen (.env/.env.local)

Erstellen Sie eine `.env.local` Datei im Projektstamm:

```env
# Beispiel für Hessische Imker Landesverband
MEINVERBAND_API_URL=https://hessen.meinverband.online/api_server.php/api
MEINVERBAND_API_USER=verein-xxx  # Ihr Vereins-User
MEINVERBAND_API_PASSWORD=your_api_password
MEINVERBAND_API_TIMEOUT=30
MEINVERBAND_API_VERIFY_SSL=true
```

**Wichtig:** Die MeinVerband API verwendet ein spezielles Format:
- Alle Requests werden als POST gesendet an `/api_server.php/api`
- JSON-Parameter im `api_request` Feld: `{"api_action": "list,verein", "api_user": "verein-120", "api_pwd": "password"}`
- Antworten enthalten CSV-Daten, die automatisch geparst werden
```

Die `.env.local` Datei sollte **nicht** ins Git-Repository committet werden (steht bereits in `.gitignore`).

## Verfügbare API-Endpunkte

Die MeinVerband API bietet folgende Endpunkte:

### Verein
- `list,verein` - Alle Vereine abrufen

### Mitglied
- `list,mitglied` - Alle Mitglieder abrufen
- `list,mitglied_funktionen` - Mitglied-Funktionen abrufen
- `list,mitglied_nur_funktionenaere` - Nur Funktionäre abrufen
- `list,mitglied_hsv_bsv_bsv_nach` - HSV/BSV Mitglieder abrufen
- `list,user` - User-Daten abrufen
- `list,user_nur_funktionenaere` - User-Funktionäre abrufen
- `list,user_hsv_bsv_bsv_nach` - User HSV/BSV abrufen

### API
- `info,api` - API-Informationen und Statistiken abrufen
```

### Vereine verwalten

```php
// Alle Vereine abrufen (api_action: list,verein)
$response = $api->verein()->getAll();

if ($response['api_status'] === 'success') {
    $vereine = $response['parsed_data'];
    
    foreach ($vereine as $verein) {
        echo $verein['verein_name'] . "\n";
        echo 'Mitglieder: ' . $verein['mitglied_count'] . "\n";
        echo 'Aktive: ' . $verein['mitglied_active_count'] . "\n\n";
    }
}
```

### Mitglieder verwalten

```php
// Alle Mitglieder abrufen
$response = $api->mitglied()->getAll();

if ($response['api_status'] === 'success') {
    $mitglieder = $response['parsed_data']; // Geparste CSV-Daten
    
    foreach ($mitglieder as $mitglied) {
        echo $mitglied['firstname'] . ' ' . $mitglied['lastname'] . "\n";
        echo 'Email: ' . $mitglied['email'] . "\n";
        echo 'Völker: ' . $mitglied['voelker'] . "\n\n";
    }
}

// Funktionäre abrufen (api_action: list,mitglied_nur_funktionenaere)
$funktionaere = $api->mitglied()->getNurFunktionaere();

// HSV/BSV Mitglieder (api_action: list,mitglied_hsv_bsv_bsv_nach)  
$hsvBsv = $api->mitglied()->getHsvBsvBsvNach();

// User-Daten abrufen (api_action: list,user)
$users = $api->mitglied()->getUsers();

// User-Funktionäre (api_action: list,user_nur_funktionenaere)
$userFunktionaere = $api->mitglied()->getUserNurFunktionaere();

// Rohdaten zugreifen
$csvData = $response['csv_data']; // Original CSV-String
$count = $response['csv_count'];   // Anzahl der Datensätze
```

### API-Informationen abrufen

```php
// API-Informationen abrufen (api_action: info,api)
$response = $api->api()->getInfo();

if ($response['api_status'] === 'success') {
    $info = $response['parsed_data'][0];
    echo 'API Modus: ' . $info['api_modus'] . "\n";
    echo 'Remote IP: ' . $info['remote_addr'] . "\n";
    
    // API-Aufruf-Statistiken
    $apiCounts = json_decode($response['api_count_json'], true);
    foreach ($apiCounts as $action => $timestamps) {
        echo "Action {$action}: " . count($timestamps) . " Aufrufe\n";
    }
}
```

## Integration in Contao

```php
<?php

// In einem Contao-Modul oder -Hook
use MeinVerband\Api\MeinVerbandApi;

class MeinVerbandModule extends \Contao\Module
{
    protected function compile()
    {
        // API-Client mit automatischer .env Konfiguration
        $api = new MeinVerbandApi();
        
        try {
            $vereinResponse = $api->verein()->getAll();
            $mitgliederResponse = $api->mitglied()->getAll();
            
            if ($vereinResponse['api_status'] === 'success') {
                $this->Template->vereine = $vereinResponse['parsed_data'];
            }
            
            if ($mitgliederResponse['api_status'] === 'success') {
                $this->Template->mitglieder = $mitgliederResponse['parsed_data'];
            }
        } catch (\Exception $e) {
            \System::log('MeinVerband API Fehler: ' . $e->getMessage(), __METHOD__, TL_ERROR);
        }
    }
}
```

## Integration in WordPress

```php
<?php

// In einem WordPress-Plugin oder Theme
add_action('init', function() {
    // API-Client mit automatischer .env Konfiguration  
    $api = new \MeinVerband\Api\MeinVerbandApi();
    
    try {
        // Vereine abrufen
        $vereinResponse = $api->verein()->getAll();
        
        // Mitglieder abrufen
        $mitgliederResponse = $api->mitglied()->getAll();
        
        // Funktionäre abrufen
        $funktionaereResponse = $api->mitglied()->getNurFunktionaere();
        
        if ($vereinResponse['api_status'] === 'success') {
            $vereine = $vereinResponse['parsed_data'];
            // Verarbeitung der Vereinsdaten...
        }
        
        if ($mitgliederResponse['api_status'] === 'success') {
            $mitglieder = $mitgliederResponse['parsed_data'];
            // Verarbeitung der Mitgliederdaten...
        }
    } catch (\Exception $e) {
        error_log('MeinVerband API Fehler: ' . $e->getMessage());
    }
});
```

## Konfiguration

Sie können den API-Client mit verschiedenen Optionen konfigurieren:

```php
$api = new MeinVerbandApi(
    'https://hessen.meinverband.online/api_server.php/api',
    'verein-120',
    'your_password',
    [
        'timeout' => 60,
        'verify' => false, // SSL-Verifizierung deaktivieren (nicht empfohlen)
    ]
);
```

## Entwicklung

### fetch_and_save.php - Test und Datenexport

Das Projekt enthält ein Utility-Skript zum Abrufen und Speichern von API-Daten:

```bash
php fetch_and_save.php
```

**Was macht das Skript:**
- Ruft alle Mitglieder über `mitglied()->getAll()` ab
- Ruft alle Vereine über `verein()->getAll()` ab
- Speichert beide als JSON-Dateien im `/tmp/` Verzeichnis
- Zeigt Fortschritt, Dateigrößen und Anzahl der Datensätze
- Lädt Konfiguration automatisch aus `.env.local`

**Ausgabedateien:**
- `tmp/mitglieder.json` - Mitgliederdaten mit `parsed_data` Array
- `tmp/vereine.json` - Vereinsdaten mit `parsed_data` Array

**Beispiel-Ausgabe:**
```
📡 Fetching data from MeinVerband API...

1️⃣ Fetching Mitglieder...
✅ Mitglieder saved to: /tmp/mitglieder.json
   Records: 217

2️⃣ Fetching Vereine...
✅ Vereine saved to: /tmp/vereine.json
   Records: 4

📁 Files in tmp directory:
   - mitglieder.json (45.3 KB)
   - vereine.json (12.7 KB)

✨ Data fetch completed successfully!
```

**Hinweis:** Das `/tmp/` Verzeichnis ist in `.gitignore` ausgeschlossen und wird nicht ins Git-Repository committed.

### Abhängigkeiten installieren

```bash
composer install
```

### Tests ausführen

```bash
composer test
```

### Code-Style prüfen

```bash
composer phpcs
```

### Statische Analyse

```bash
composer phpstan
```

## Vollständiges Beispiel

```php
<?php

require 'vendor/autoload.php';

use MeinVerband\Api\MeinVerbandApi;

// API-Client initialisieren
$api = new MeinVerbandApi();

try {
    // Vereinsdaten abrufen
    $vereinResponse = $api->verein()->getAll();
    if ($vereinResponse['api_status'] === 'success') {
        $vereine = $vereinResponse['parsed_data'];
        echo "Gefundene Vereine: " . count($vereine) . "\n";
    }
    
    // Alle Mitglieder abrufen  
    $mitgliederResponse = $api->mitglied()->getAll();
    if ($mitgliederResponse['api_status'] === 'success') {
        $mitglieder = $mitgliederResponse['parsed_data'];
        echo "Gefundene Mitglieder: " . count($mitglieder) . "\n";
    }
    
    // Nur Funktionäre abrufen
    $funktionaereResponse = $api->mitglied()->getNurFunktionaere();
    if ($funktionaereResponse['api_status'] === 'success') {
        $funktionaere = $funktionaereResponse['parsed_data'];
        echo "Gefundene Funktionäre: " . count($funktionaere) . "\n";
    }
    
    // API-Informationen abrufen
    $apiResponse = $api->api()->getInfo();
    if ($apiResponse['api_status'] === 'success') {
        $info = $apiResponse['parsed_data'][0];
        echo "API Modus: " . $info['api_modus'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}
```

## Über das Projekt

Diese Bibliothek ermöglicht die Integration mit der VerbandsCloud des Deutschen Imkerbundes. Die VerbandsCloud wird von der **syscape® GmbH** bereitgestellt und verwaltet Vereins- und Mitgliederdaten für Imkervereine deutschlandweit.

### Technische Details
- **API-Provider:** syscape® GmbH
- **Auftraggeber:** Deutscher Imkerbund 
- **User-Format:** verein-xxx (entsprechend Ihrem Vereins-Code)
- **Domain-Struktur:** `[landesverband].meinverband.online`

## Lizenz

MIT License