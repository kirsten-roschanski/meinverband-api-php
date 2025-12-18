<?php

declare(strict_types=1);

namespace MeinVerband\Api;

/**
 * Configuration helper for MeinVerband API
 * 
 * Loads configuration from environment variables (.env/.env.local files)
 */
class Config
{
    private static ?array $config = null;
    private static bool $loaded = false;

    /**
     * Load configuration from .env files
     */
    public static function load(?string $envFile = null): void
    {
        if (self::$loaded) {
            return;
        }

        $basePath = $envFile ? dirname($envFile) : getcwd();
        
        // Load .env first (default values)
        self::loadEnvFile($basePath . '/.env');
        
        // Load .env.local (overrides)
        self::loadEnvFile($basePath . '/.env.local');
        
        self::$loaded = true;
    }

    /**
     * Get configuration value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) {
            self::load();
        }

        return $_ENV[$key] ?? $default;
    }

    /**
     * Get API base URL
     */
    public static function getApiUrl(): string
    {
        return self::get('MEINVERBAND_API_URL', 'https://api.meinverband.online');
    }

    /**
     * Get API username
     */
    public static function getApiUser(): ?string
    {
        return self::get('MEINVERBAND_API_USER');
    }

    /**
     * Get API password
     */
    public static function getApiPassword(): ?string
    {
        return self::get('MEINVERBAND_API_PASSWORD');
    }

    /**
     * Get API timeout
     */
    public static function getApiTimeout(): int
    {
        return (int) self::get('MEINVERBAND_API_TIMEOUT', 30);
    }

    /**
     * Get SSL verification setting
     */
    public static function getApiVerifySsl(): bool
    {
        $value = self::get('MEINVERBAND_API_VERIFY_SSL', 'true');
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get complete API configuration array
     */
    public static function getApiConfig(): array
    {
        return [
            'base_url' => self::getApiUrl(),
            'username' => self::getApiUser(),
            'password' => self::getApiPassword(),
            'timeout' => self::getApiTimeout(),
            'verify' => self::getApiVerifySsl(),
        ];
    }

    /**
     * Load environment variables from a .env file
     */
    private static function loadEnvFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }
                
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}