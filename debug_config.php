<?php

require 'vendor/autoload.php';

use MeinVerband\Api\Config;

echo "Config Test\n";
echo "===========\n\n";

// Load config
Config::load();

echo "API URL: " . Config::getApiUrl() . "\n";
echo "API User: " . Config::getApiUser() . "\n"; 
echo "API Password: " . (Config::getApiPassword() ? '[GESETZT]' : '[NICHT GESETZT]') . "\n";
echo "API Timeout: " . Config::getApiTimeout() . "\n";
echo "SSL Verify: " . (Config::getApiVerifySsl() ? 'true' : 'false') . "\n";

echo "\nEnvironment Variables:\n";
echo "MEINVERBAND_API_URL: " . ($_ENV['MEINVERBAND_API_URL'] ?? 'NOT SET') . "\n";
echo "MEINVERBAND_API_USER: " . ($_ENV['MEINVERBAND_API_USER'] ?? 'NOT SET') . "\n";
echo "MEINVERBAND_API_PASSWORD: " . ($_ENV['MEINVERBAND_API_PASSWORD'] ?? 'NOT SET') . "\n";