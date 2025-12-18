<?php

declare(strict_types=1);

namespace MeinVerband\Api\Endpoints;

use MeinVerband\Api\ApiClient;

/**
 * API endpoint handler for meta operations
 */
class Api
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get API information (api_action: info,api)
     */
    public function getInfo(): array
    {
        return $this->client->get('api');
    }
}