<?php

declare(strict_types=1);

namespace MeinVerband\Api\Endpoints;

use MeinVerband\Api\ApiClient;

/**
 * Verein endpoint handler
 */
class Verein
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get all Vereine (api_action: list,verein)
     */
    public function getAll(): array
    {
        return $this->client->get('verein');
    }
}