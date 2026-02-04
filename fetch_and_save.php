<?php

declare(strict_types=1);

/**
 * Test script to fetch data from MeinVerband API and save to tmp directory
 */

require_once __DIR__ . '/vendor/autoload.php';

use MeinVerband\Api\MeinVerbandApi;

// Load .env.local manually
if (file_exists(__DIR__ . '/.env.local')) {
    $envContent = file_get_contents(__DIR__ . '/.env.local');
    foreach (explode("\n", $envContent) as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#')) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Create tmp directory if it doesn't exist
$tmpDir = __DIR__ . '/tmp';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0755, true);
}

try {
    // Initialize API with credentials from .env.local
    $api = new MeinVerbandApi(
        baseUrl: $_ENV['MEINVERBAND_API_URL'] ?? 'https://example.com',
        username: $_ENV['MEINVERBAND_API_USER'] ?? '',
        password: $_ENV['MEINVERBAND_API_PASSWORD'] ?? ''
    );

    echo "📡 Fetching data from MeinVerband API...\n\n";

    // Fetch Mitglieder (Members)
    echo "1️⃣ Fetching Mitglieder...\n";
    try {
        $mitgliederResponse = $api->mitglied()->getAll();
        $mitgliederFile = $tmpDir . '/mitglieder.json';
        file_put_contents($mitgliederFile, json_encode($mitgliederResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "✅ Mitglieder saved to: $mitgliederFile\n";
        
        // Extract parsed data if available
        if (isset($mitgliederResponse['parsed_data']) && is_array($mitgliederResponse['parsed_data'])) {
            echo "   Records: " . count($mitgliederResponse['parsed_data']) . "\n";
        } elseif (is_array($mitgliederResponse)) {
            echo "   Records: " . count($mitgliederResponse) . "\n";
        }
        echo "\n";
    } catch (\Exception $e) {
        echo "❌ Error fetching Mitglieder: " . $e->getMessage() . "\n\n";
    }

    // Fetch Vereine (Clubs/Associations)
    echo "2️⃣ Fetching Vereine...\n";
    try {
        $vereineResponse = $api->verein()->getAll();
        $vereineFile = $tmpDir . '/vereine.json';
        file_put_contents($vereineFile, json_encode($vereineResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "✅ Vereine saved to: $vereineFile\n";
        
        // Extract parsed data if available
        if (isset($vereineResponse['parsed_data']) && is_array($vereineResponse['parsed_data'])) {
            echo "   Records: " . count($vereineResponse['parsed_data']) . "\n";
        } elseif (is_array($vereineResponse)) {
            echo "   Records: " . count($vereineResponse) . "\n";
        }
        echo "\n";
    } catch (\Exception $e) {
        echo "❌ Error fetching Vereine: " . $e->getMessage() . "\n\n";
    }

    // List files in tmp directory
    echo "📁 Files in tmp directory:\n";
    $files = array_diff(scandir($tmpDir), ['.', '..']);
    if (empty($files)) {
        echo "   (no files yet)\n";
    } else {
        foreach ($files as $file) {
            $filePath = $tmpDir . '/' . $file;
            $size = filesize($filePath);
            $formattedSize = $size > 1024 
                ? round($size / 1024, 2) . ' KB' 
                : $size . ' B';
            echo "   - $file ($formattedSize)\n";
        }
    }
    echo "\n✨ Data fetch completed successfully!\n";

} catch (\Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
