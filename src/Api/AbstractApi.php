<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Api;


use CloudPlayDev\ConfluenceClient\ConfluenceClient;
use Http\Client\Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use function array_filter;
use function array_merge;
use function json_encode;
use function rawurlencode;
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
    protected function get(string $uri, array $params = [], array $headers = []): ResponseInterface
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
    protected function put(string $uri, array $params = [], array $headers = []): ResponseInterface
    {
        $body = self::prepareJsonBody($params);

        if ($body !== '') {
            $headers = self::addJsonContentType($headers);
        }

        return $this->client->getHttpClient()->put(self::prepareUri($uri), $headers, $body);
    }

    /**
     * @param string $uri
     * @param mixed[] $params
     * @param array<string, string> $headers
     * @return ResponseInterface
     * @throws Exception
     * @throws JsonException
     */
    protected function post(string $uri, array $params = [], array $headers = []): ResponseInterface
    {
        $body = self::prepareJsonBody($params);

        if ($body !== '') {
            $headers = self::addJsonContentType($headers);
        }

        return $this->client->getHttpClient()->post(self::prepareUri($uri), $headers, $body);
    }

    /**
     * @param string $uri
     * @param mixed[] $params
     * @param array<string, string> $headers
     * @return ResponseInterface
     * @throws Exception
     * @throws JsonException
     */
    protected function delete(string $uri, array $params = [], array $headers = []): ResponseInterface
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
        $query = array_filter($query, static function ($value): bool {
            return null !== $value;
        });

        return sprintf('%s%s%s', self::URI_PREFIX, $uri, http_build_query($query));
    }

    /**
     * @param mixed $uri
     * @return string
     */
    protected static function encodePath($uri): string
    {
        return rawurlencode((string)$uri);
    }
}