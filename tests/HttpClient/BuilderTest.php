<?php
declare(strict_types=1);

namespace CloudPlayDev\Tests\ConfluenceClient\HttpClient;

use CloudPlayDev\ConfluenceClient\HttpClient\Builder;
use Http\Client\Common\Plugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class BuilderTest extends TestCase
{

    /**
     * @var Builder
     */
    private Builder $subject;

    /**
     * @var mixed|MockObject|UriFactoryInterface
     */
    private mixed $uriFactory;

    /**
     * @before
     */
    public function initBuilder(): void
    {
        $this->uriFactory = $this->createMock(UriFactoryInterface::class);
        $this->subject = new Builder(
            $this->createMock(ClientInterface::class),
            $this->createMock(RequestFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class),
            $this->uriFactory
        );
    }

    public function testAddPluginShouldInvalidateHttpClient(): void
    {
        $client = $this->subject->getHttpClient();

        $this->subject->addPlugin($this->createMock(Plugin::class));

        self::assertNotSame($client, $this->subject->getHttpClient());
    }

    public function testRemovePluginShouldInvalidateHttpClient(): void
    {
        $this->subject->addPlugin($this->createMock(Plugin::class));

        $client = $this->subject->getHttpClient();

        $this->subject->removePlugin(Plugin::class);

        self::assertNotSame($client, $this->subject->getHttpClient());
    }

    public function testCanGetUriFactory(): void
    {
        self::assertSame($this->uriFactory, $this->subject->getUriFactory());
    }
}
