<?php

declare(strict_types=1);

namespace MeinVerband\Api;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Main API client for MeinVerband
 * 
 * This class provides a simple interface to interact with the MeinVerband API
 * and can be easily integrated into Contao or WordPress projects.
 */
class ApiClient
{
    private Client $httpClient;
    private LoggerInterface $logger;
    private string $baseUrl;
    private array $defaultHeaders;
    private ?string $apiUser;
    private ?string $apiPassword;

    public function __construct(
        ?string $baseUrl = null,
        ?string $username = null,
        ?string $password = null,
        array $config = [],
        ?LoggerInterface $logger = null
    ) {
        // Load configuration from environment if not provided
        Config::load();
        
        $this->baseUrl = rtrim($baseUrl ?? Config::getApiUrl(), '/');
        $this->logger = $logger ?? new NullLogger();
        
        $defaultConfig = [
            'timeout' => Config::getApiTimeout(),
            'verify' => Config::getApiVerifySsl(),
        ];
        
        // Store credentials for API requests
        $this->apiUser = $username ?? Config::getApiUser();
        $this->apiPassword = $password ?? Config::getApiPassword();
        
        $this->httpClient = new Client(array_merge($defaultConfig, $config));
        
        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'User-Agent' => 'MeinVerband-API-Client/1.0'
        ];
    }

    /**
     * Make a GET request to the API (converted to POST for MeinVerband API)
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->apiRequest($endpoint, $params);
    }

    /**
     * Make a POST request to the API
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->apiRequest($endpoint, $data, 'create');
    }

    /**
     * Make a PUT request to the API (converted to POST for MeinVerband API)
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->apiRequest($endpoint, $data, 'update');
    }

    /**
     * Make a DELETE request to the API (converted to POST for MeinVerband API)
     */
    public function delete(string $endpoint): array
    {
        return $this->apiRequest($endpoint, [], 'delete');
    }

    /**
     * Set authentication token
     */
    public function setAuthToken(string $token): self
    {
        $this->defaultHeaders['Authorization'] = 'Bearer ' . $token;
        return $this;
    }

    /**
     * MeinVerband API request method
     */
    private function apiRequest(string $endpoint, array $data = [], string $action = 'list'): array
    {
        if (!$this->apiUser || !$this->apiPassword) {
            throw new ApiException("API credentials not configured. Please set MEINVERBAND_API_USER and MEINVERBAND_API_PASSWORD in .env.local");
        }

        // Build api_action based on endpoint and action
        $apiAction = $this->buildApiAction($endpoint, $action, $data);
        
        $apiRequestData = [
            'api_action' => $apiAction,
            'api_user' => $this->apiUser,
            'api_pwd' => $this->apiPassword,
        ];
        
        // Add additional data for create/update operations
        if (!empty($data) && in_array($action, ['create', 'update'])) {
            $apiRequestData = array_merge($apiRequestData, $data);
        }

        $options = [
            'headers' => $this->defaultHeaders,
            'form_params' => [
                'api_request' => json_encode($apiRequestData)
            ]
        ];

        try {
            $this->logger->info("Making MeinVerband API request with action: {$apiAction}");
            $this->logger->info("API Request Data: " . json_encode($apiRequestData, JSON_UNESCAPED_UNICODE));
            
            $response = $this->httpClient->request('POST', $this->baseUrl, $options);
            $body = $response->getBody()->getContents();
            
            $this->logger->info("Received response with status {$response->getStatusCode()}");
            
            $result = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If not JSON, return raw response
                return ['raw_response' => $body];
            }
            
            // Parse CSV data if present and successful
            if (isset($result['api_status']) && $result['api_status'] === 'success' && isset($result['csv_data'])) {
                $result['parsed_data'] = $this->parseCsvData($result['csv_data']);
            }
            
            return $result ?? [];
            
        } catch (GuzzleException $e) {
            $this->logger->error("MeinVerband API request failed: " . $e->getMessage());
            throw new ApiException("MeinVerband API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Build api_action parameter based on endpoint and action
     */
    private function buildApiAction(string $endpoint, string $action, array $data = []): string
    {
        // Remove leading/trailing slashes and convert to lowercase
        $endpoint = strtolower(trim($endpoint, '/'));
        
        // Map specific endpoints to their API actions
        $endpointMap = [
            // Verein endpoints
            'verein' => 'list,verein',
            
            // Mitglied endpoints  
            'mitglied' => 'list,mitglied',
            'mitglied_funktionen' => 'list,mitglied_funktionen',
            'mitglied_nur_funktionenaere' => 'list,mitglied_nur_funktionenaere',
            'mitglied_hsv_bsv_bsv_nach' => 'list,mitglied_hsv_bsv_bsv_nach',
            
            // User endpoints
            'user' => 'list,user',
            'user_nur_funktionenaere' => 'list,user_nur_funktionenaere', 
            'user_hsv_bsv_bsv_nach' => 'list,user_hsv_bsv_bsv_nach',
            
            // API endpoint
            'api' => 'info,api',
        ];
        
        // Return mapped action or default to list
        if (isset($endpointMap[$endpoint])) {
            return $endpointMap[$endpoint];
        }
        
        // Fallback for unknown endpoints
        return "list,{$endpoint}";
    }

    /**
     * Parse CSV data into associative array
     */
    private function parseCsvData(string $csvData): array
    {
        if (empty($csvData)) {
            return [];
        }

        $lines = explode("\n", trim($csvData));
        if (empty($lines)) {
            return [];
        }

        // Parse header row
        $headers = str_getcsv(array_shift($lines), ';');
        $headers = array_map(function($header) {
            return trim($header, '"');
        }, $headers);

        $data = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            
            $values = str_getcsv($line, ';');
            $values = array_map(function($value) {
                return trim($value, '"');
            }, $values);
            
            if (count($values) === count($headers)) {
                $data[] = array_combine($headers, $values);
            }
        }

        return $data;
    }

    /**
     * Generic request method (kept for compatibility)
     */
    private function request(string $method, string $endpoint, array $options = []): array
    {
        // Delegate to apiRequest for MeinVerband API compatibility
        return $this->apiRequest($endpoint, $options['json'] ?? [], strtolower($method));
    }
}