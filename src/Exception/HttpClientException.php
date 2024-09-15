<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Exception;


use Psr\Http\Message\ResponseInterface;
use Throwable;
use Webmozart\Assert\Assert;

class HttpClientException extends ConfluencePhpClientException
{
    private ResponseInterface $response;

    public function __construct(string $message, int $code, ResponseInterface $response)
    {
        parent::__construct($message, $code);
        $this->response = $response;
    }

    public static function badRequest(ResponseInterface $response): HttpClientException
    {
        $validationMessage = self::extractValidationMessage($response);

        $message = sprintf("The parameters passed to the API were invalid. Check your inputs!\n\n%s", $validationMessage);

        return new self($message, 400, $response);
    }

    public static function unauthorized(ResponseInterface $response): HttpClientException
    {
        return new self('Your credentials are incorrect.', 401, $response);
    }

    public static function requestFailed(ResponseInterface $response): HttpClientException
    {
        return new self('Parameters were valid but request failed. Try again.', 402, $response);
    }

    public static function notFound(ResponseInterface $response): HttpClientException
    {
        return new self('The endpoint you have tried to access does not exist.', 404, $response);
    }

    public static function conflict(ResponseInterface $response): HttpClientException
    {
        return new self('Request conflicts with current state of the target resource.', 409, $response);
    }

    public static function payloadTooLarge(ResponseInterface $response): HttpClientException
    {
        return new self('Payload too large, your total attachment size is too big.', 413, $response);
    }

    public static function tooManyRequests(ResponseInterface $response): HttpClientException
    {
        return new self('Too many requests.', 429, $response);
    }

    public static function forbidden(ResponseInterface $response): HttpClientException
    {
        $validationMessage = self::extractValidationMessage($response);

        $message = sprintf("Forbidden!\n\n%s", $validationMessage);

        return new self($message, 403, $response);
    }

    /**
     * @param ResponseInterface $response
     * @param string $jsonField
     * @return string
     */
    private static function extractValidationMessage(ResponseInterface $response, string $jsonField = 'message'): string
    {

        $validationMessage = $response->getBody()->getContents();

        try {
            if (str_starts_with($response->getHeaderLine('Content-Type'), 'application/json')) {
                $jsonDecoded = json_decode($validationMessage, true, 512, JSON_THROW_ON_ERROR);
                Assert::isArray($jsonDecoded);
                Assert::keyExists($jsonDecoded, $jsonField);
                Assert::string($jsonDecoded[$jsonField]);
                $validationMessage = $jsonDecoded[$jsonField];
            }
        } catch (Throwable) {
            return $validationMessage;
        }

        return $validationMessage;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

}
