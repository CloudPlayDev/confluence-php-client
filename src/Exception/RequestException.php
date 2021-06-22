<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Exception;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RequestException extends Exception implements ClientExceptionInterface
{
    public function __construct(ResponseInterface $response, Throwable $previous = null)
    {
        parent::__construct($response->getBody()->getContents(), $response->getStatusCode(), $previous);
    }

}