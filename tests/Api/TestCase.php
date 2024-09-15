<?php
declare(strict_types=1);

namespace CloudPlayDev\Tests\ConfluenceClient\Api;

use CloudPlayDev\ConfluenceClient\Api\Content;
use CloudPlayDev\ConfluenceClient\ConfluenceClient;
use CloudPlayDev\ConfluenceClient\HttpClient\Builder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use function array_merge;

abstract class TestCase extends BaseTestCase
{

    /**
     * @return string
     */
    abstract protected function getApiClass(): string;

    /**
     * @param array $methods
     *
     * @return MockObject|Content
     */
    protected function getApiMock(array $methods = []): MockObject|Content
    {
        $httpClient = $this->getMockBuilder(ClientInterface::class)
            ->onlyMethods(['sendRequest'])
            ->getMock();
        $httpClient
            ->method('sendRequest');


        $builder = new Builder($httpClient);
        $client = new ConfluenceClient('https://example.com', $builder);

        return $this->getMockBuilder($this->getApiClass())
            ->onlyMethods(array_merge(['httpGet', 'httpPost', 'httpDelete', 'httpPut'], $methods))
            ->setConstructorArgs([$client])
            ->getMock();
    }

    /**
     * @param string $responseString
     * @param int $responseCode
     * @param string $contentType
     * @return mixed|MockObject|ResponseInterface
     */
    protected function createResponse(string $responseString, int $responseCode = 200, string $contentType = 'application/json'): mixed
    {

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamInterface->method('getContents')->willReturn($responseString);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($streamInterface);
        $response->method('getStatusCode')->willReturn($responseCode);
        $response->method('getHeaderLine')->with('Content-Type')->willReturn($contentType);

        return $response;


    }
}
