<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $statusCode;

    public function __construct($message, int $statusCode = 500)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
