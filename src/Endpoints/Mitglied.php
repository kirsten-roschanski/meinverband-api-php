<?php

declare(strict_types=1);

namespace MeinVerband\Api\Endpoints;

use MeinVerband\Api\ApiClient;

/**
 * Mitglied endpoint handler
 */
class Mitglied
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get all Mitglieder (api_action: list,mitglied)
     */
    public function getAll(): array
    {
        return $this->client->get('mitglied');
    }

    /**
     * Get Mitglied functions (api_action: list,mitglied_funktionen)
     */
    public function getFunktionen(): array
    {
        return $this->client->get('mitglied_funktionen');
    }

    /**
     * Get only Funktionäre (api_action: list,mitglied_nur_funktionenaere)
     */
    public function getNurFunktionaere(): array
    {
        return $this->client->get('mitglied_nur_funktionenaere');
    }

    /**
     * Get HSV/BSV members (api_action: list,mitglied_hsv_bsv_bsv_nach)
     */
    public function getHsvBsvBsvNach(): array
    {
        return $this->client->get('mitglied_hsv_bsv_bsv_nach');
    }

    /**
     * Get users (api_action: list,user)
     */
    public function getUsers(): array
    {
        return $this->client->get('user');
    }

    /**
     * Get only user Funktionäre (api_action: list,user_nur_funktionenaere)
     */
    public function getUserNurFunktionaere(): array
    {
        return $this->client->get('user_nur_funktionenaere');
    }

    /**
     * Get user HSV/BSV (api_action: list,user_hsv_bsv_bsv_nach)
     */
    public function getUserHsvBsvBsvNach(): array
    {
        return $this->client->get('user_hsv_bsv_bsv_nach');
    }
}