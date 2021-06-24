<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Api;


use CloudPlayDev\ConfluenceClient\ConfluenceClient;
use CloudPlayDev\ConfluenceClient\Entity\Hydratable;
use CloudPlayDev\ConfluenceClient\Exception\ConfluencePhpClientException;
use CloudPlayDev\ConfluenceClient\Exception\HttpClientException;
use CloudPlayDev\ConfluenceClient\Exception\HttpServerException;
use CloudPlayDev\ConfluenceClient\Exception\HydrationException;
use Http\Client\Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;
use function array_filter;
use function array_merge;
use function get_class;
use function in_array;
use function json_encode;
use function sprintf;

abstract class AbstractApi
{
    /**
     * default rest API prefix for confluence
     */
    private const URI_PREFIX = '/rest/api/';

    private ConfluenceClient $client;

    public function __construct(ConfluenceClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $uri
     * @param mixed[] $params
     * @param array<string, string> $headers
     * @return ResponseInterface
     * @throws Exception
     */
    protected function httpGet(string $uri, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->getHttpClient()->get(self::prepareUri($uri, $params), $headers);
    }

    /**
     * @param string $uri
     * @param mixed[] $params
     * @param array<string, string> $headers
     * @return ResponseInterface
     * @throws Exception
     * @throws JsonException
     */
    protected function httpPut(string $uri, array $params = [], array $headers = []): ResponseInterface
    {
        $body = self::prepareJsonBody($params);

        if ($body !== '') {
            $headers = self::addJsonContentType($headers);
        }

        return $this->client->getHttpClient()->put(self::prepareUri($uri), $headers, $body);
    }

    /**
     * @param string $uri
     * @param mixed[] $queryParams
     * @param mixed[] $bodyData
     * @param array<string, string> $headers
     * @return ResponseInterface
     * @throws Exception
     * @throws JsonException
     */
    protected function httpPost(string $uri, array $queryParams = [], array $bodyData = [], array $headers = []): ResponseInterface
    {
        $body = self::prepareJsonBody($bodyData);

        if ($body !== '') {
            $headers = self::addJsonContentType($headers);
        }

        return $this->client->getHttpClient()->post(self::prepareUri($uri, $queryParams), $headers, $body);
    }

    /**
     * @param string $uri
     * @param mixed[] $params
     * @param array<string, string> $headers
     * @return ResponseInterface
     * @throws Exception
     * @throws JsonException
     */
    protected function httpDelete(string $uri, array $params = [], array $headers = []): ResponseInterface
    {
        $body = self::prepareJsonBody($params);

        if ($body !== '') {
            $headers = self::addJsonContentType($headers);
        }

        return $this->client->getHttpClient()->delete(self::prepareUri($uri), $headers, $body);
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    private static function addJsonContentType(array $headers): array
    {
        return array_merge(['Content-Type' => 'application/json'], $headers);
    }

    /**
     * @param mixed[] $params
     * @return string
     * @throws JsonException
     */
    private static function prepareJsonBody(array $params): string
    {
        return json_encode($params, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $uri
     * @param mixed[] $query
     * @return string
     */
    private static function prepareUri(string $uri, array $query = []): string
    {
        $query = array_filter($query, static function($value): bool {
            return null !== $value;
        });

        $httpQueryParameter = http_build_query($query);
        if ($httpQueryParameter !== '') {
            $uri .= '?';
        }
        return sprintf('%s%s%s', self::URI_PREFIX, $uri, $httpQueryParameter);
    }

    /**
     * @psalm-template ExpectedType of Hydratable
     * @psalm-param class-string<ExpectedType> $class
     * @psalm-return ExpectedType
     *
     * @psalm-suppress InvalidReturnType https://psalm.dev/r/46c8264450
     * @psalm-suppress InvalidReturnStatement https://psalm.dev/r/46c8264450
     *
     * @param ResponseInterface $response
     * @param class-string $class
     * @return Hydratable
     * @throws ConfluencePhpClientException
     * @throws HttpClientException
     * @throws HttpServerException
     * @throws HydrationException
     * @throws JsonException
     */
    protected function hydrateResponse(ResponseInterface $response, string $class): Hydratable
    {
        $this->handleErrors($response);

        $contentType = $response->getHeaderLine('Content-Type');

        if (!str_starts_with($contentType, 'application/json')) {
            throw new HydrationException('The ModelHydrator cannot hydrate response with Content-Type: ' . $contentType);
        }
        if (!is_subclass_of($class, Hydratable::class)) {
            throw new HydrationException('This class can not be hydrated: ' . $class);
        }

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        Assert::isArray($data);

        $entity = $class::load($data);
        if (!$entity instanceof $class) {
            throw new HydrationException('An unexpected class was created: ' . get_class($entity));
        }
        return $entity;
    }

    /**
     * Throw the correct exception for this error.
     */
    protected function handleErrors(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if (in_array($response->getStatusCode(), [200, 201, 202, 204], true)) {
            return;
        }

        switch ($statusCode) {
            case 400:
                throw HttpClientException::badRequest($response);
            case 401:
                throw HttpClientException::unauthorized($response);
            case 402:
                throw HttpClientException::requestFailed($response);
            case 403:
                throw HttpClientException::forbidden($response);
            case 404:
                throw HttpClientException::notFound($response);
            case 409:
                throw HttpClientException::conflict($response);
            case 413:
                throw HttpClientException::payloadTooLarge($response);
            case 429:
                throw HttpClientException::tooManyRequests($response);
            case 500 <= $statusCode:
                throw HttpServerException::serverError($response);
            default:
                throw new ConfluencePhpClientException($response->getBody()->getContents(), $response->getStatusCode());
        }
    }

    /**
     * @param string|int|null ...$parameter
     * @return string
     */
    protected static function getRestfulUri(...$parameter): string
    {
        $parameterString = implode('/', array_filter($parameter));

        if (!empty($parameterString)) {
            return '/' . $parameterString;
        }

        return '';
    }
}
