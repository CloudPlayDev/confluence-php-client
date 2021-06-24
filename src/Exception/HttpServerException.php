<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Exception;


use Psr\Http\Message\ResponseInterface;

class HttpServerException extends ConfluencePhpClientException
{
    public static function serverError(ResponseInterface $response): HttpServerException
    {
        $httpStatus = $response->getStatusCode();
        return new self('An unexpected error occurred. Try again later.', $httpStatus);
    }
}
