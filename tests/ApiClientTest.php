<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MeinVerband\Api\ApiClient;
use MeinVerband\Api\ApiException;
use MeinVerband\Api\MeinVerbandApi;

class ApiClientTest extends TestCase
{
    private ApiClient $apiClient;
    private MeinVerbandApi $api;

    protected function setUp(): void
    {
        $this->apiClient = new ApiClient('https://api.example.com');
        $this->api = new MeinVerbandApi('https://api.example.com');
    }

    public function testApiClientConstructor(): void
    {
        $this->assertInstanceOf(ApiClient::class, $this->apiClient);
    }

    public function testMeinVerbandApiConstructor(): void
    {
        $this->assertInstanceOf(MeinVerbandApi::class, $this->api);
    }

    public function testSetAuthToken(): void
    {
        $result = $this->apiClient->setAuthToken('test-token');
        $this->assertInstanceOf(ApiClient::class, $result);
    }

    public function testVereinEndpoint(): void
    {
        $verein = $this->api->verein();
        $this->assertInstanceOf(\MeinVerband\Api\Endpoints\Verein::class, $verein);
    }

    public function testMitgliedEndpoint(): void
    {
        $mitglied = $this->api->mitglied();
        $this->assertInstanceOf(\MeinVerband\Api\Endpoints\Mitglied::class, $mitglied);
    }



    public function testApiEndpoint(): void
    {
        $api = $this->api->api();
        $this->assertInstanceOf(\MeinVerband\Api\Endpoints\Api::class, $api);
    }
}