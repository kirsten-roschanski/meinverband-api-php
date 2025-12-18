<?php

declare(strict_types=1);

namespace MeinVerband\Api;

use Exception;

/**
 * Custom exception for API-related errors
 */
class ApiException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}