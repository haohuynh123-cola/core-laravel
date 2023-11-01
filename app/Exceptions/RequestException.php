<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class RequestException extends \Exception
{
    public $response;

    public function __construct(string $message, $response, int $code = Response::HTTP_BAD_REQUEST)
    {
        $message = 'Request: ' . $message;
        $this->response = $response;
        parent::__construct($message, $code);
    }
}
