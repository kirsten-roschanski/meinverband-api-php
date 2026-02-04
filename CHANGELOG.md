# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

## [1.0.1] - 2026-02-04

### Added

- **fetch_and_save.php** - Neues Test-Skript zum Abrufen und Speichern von API-Daten
  - Holt Mitglieder via `$api->mitglied()->getAll()`
  - Holt Vereine via `$api->verein()->getAll()`
  - Speichert Daten als JSON in `/tmp/` Verzeichnis
  - Lädt `.env.local` Konfiguration automatisch
  - Zeigt Fortschritt und Dateigrößen an
  - Umfassende Fehlerbehandlung

### Changed

- **ApiClient.php** - `parseCsvData()` Methode optimiert
  - Verwendung von `fgetcsv()` statt `explode()` für robusteres CSV-Parsing
  - Unterstützt nun korrekt Zeilenumbrüche innerhalb von angeführten Feldern
  - Bessere Handhabung von Edge-Cases in CSV-Daten
  - Verbesserte Performance durch Memory-Stream-Nutzung

### Fixed

- CSV-Parsing-Fehler bei Feldern mit Zeilenumbrüchen in Anführungszeichen
  - Altes Problem: `explode("\n")` konnte mehrzeilige Felder nicht korrekt verarbeiten
  - Neue Lösung: `fgetcsv()` mit Memory-Stream für korrekte CSV-Format-Verarbeitung

### Security

- **.gitignore** - `/tmp/` Verzeichnis hinzugefügt
  - Verhindert, dass temporäre/Test-Daten in Git committed werden
  - Schützt sensible lokale Testdaten

## [1.0.0] - Initial Release

- Initial project setup mit PSR-4 Autoloading
- MeinVerbandApi Facade-Klasse
- ApiClient mit Guzzle HTTP Client
- Endpoints: Mitglied, Verein, Api
- Config-Management mit .env.local Support
- PHPUnit Tests
