<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    protected $statusCode;
    protected $errorDetails;

    public function __construct($message, $statusCode = 500, $errorDetails = [])
    {
        parent::__construct($message);

        $this->statusCode = $statusCode;
        $this->errorDetails = $errorDetails;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getErrorDetails()
    {
        return $this->errorDetails;
    }
}
