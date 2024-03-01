<?php

namespace CloudPlayDev\Tests\ConfluenceClient;

use CloudPlayDev\ConfluenceClient\Api\Content;
use CloudPlayDev\ConfluenceClient\ConfluenceClient;
use CloudPlayDev\ConfluenceClient\HttpClient\Builder;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriFactoryInterface;

class ConfluenceClientTest extends TestCase
{

    public function testCanUseCustomClientBuilder(): void
    {
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('getHttpClient');

        $client = new ConfluenceClient('https://example.com', $builder);
        $client->getHttpClient();
    }

    public function testCanUseBasicAuth(): void
    {
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->atLeast(2))
            ->method('addPlugin');

        $builder->expects($this->atLeast(2))
            ->method('removePlugin');

        $client = new ConfluenceClient('https://example.com', $builder);
        $client->authenticateBasicAuth('username', 'password');
    }

    public function testCanUseAuth(): void
    {
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->atLeast(2))
            ->method('addPlugin');

        $builder->expects($this->atLeast(2))
            ->method('removePlugin');

        $client = new ConfluenceClient('https://example.com', $builder);
        $client->authenticate('token');
    }

    public function testCanUseUsernameAndPasswortInUri(): void
    {
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->exactly(4))
            ->method('addPlugin');

        $builder->expects($this->exactly(2))
            ->method('removePlugin');

        $urlFactory = $this->createMock(UriFactoryInterface::class);
        $urlFactory->method('createUri')->willReturn(new Uri('https://username:password@example.com/somepath/'));

        $builder->method('getUriFactory')->willReturn($urlFactory);

        new ConfluenceClient('https://username:password@example.com', $builder);
    }

    public function testCanGetContent(): void
    {
        $builder = $this->createMock(Builder::class);

        $client = new ConfluenceClient('https://example.com', $builder);
        $content = $client->content();

        self::assertInstanceOf(Content::class, $content);
    }
}
