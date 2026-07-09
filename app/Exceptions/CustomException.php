<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CustomException extends Exception
{
    public function __construct(
        string $message,
        protected int $statusCode = Response::HTTP_BAD_REQUEST,
        protected mixed $data = null,
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
