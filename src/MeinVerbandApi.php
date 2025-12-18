<?php

declare(strict_types=1);

namespace MeinVerband\Api;

use MeinVerband\Api\Endpoints\Mitglied;
use MeinVerband\Api\Endpoints\Verein;
use MeinVerband\Api\Endpoints\Api;

/**
 * Main MeinVerband API facade class
 * 
 * This class provides easy access to all API endpoints and is the main
 * entry point for Contao and WordPress integrations.
 */
class MeinVerbandApi
{
    private ApiClient $client;
    private Mitglied $mitglied;
    private Verein $verein;
    private Api $api;

    public function __construct(
        ?string $baseUrl = null,
        ?string $username = null,
        ?string $password = null,
        array $config = []
    ) {
        $this->client = new ApiClient($baseUrl, $username, $password, $config);
        $this->mitglied = new Mitglied($this->client);
        $this->verein = new Verein($this->client);
        $this->api = new Api($this->client);
    }

    /**
     * Set authentication token
     */
    public function setAuthToken(string $token): self
    {
        $this->client->setAuthToken($token);
        return $this;
    }

    /**
     * Get Mitglied endpoint
     */
    public function mitglied(): Mitglied
    {
        return $this->mitglied;
    }

    /**
     * Get Verein endpoint
     */
    public function verein(): Verein
    {
        return $this->verein;
    }

    /**
     * Get API endpoint
     */
    public function api(): Api
    {
        return $this->api;
    }

    /**
     * Get the underlying API client for custom requests
     */
    public function getClient(): ApiClient
    {
        return $this->client;
    }
}