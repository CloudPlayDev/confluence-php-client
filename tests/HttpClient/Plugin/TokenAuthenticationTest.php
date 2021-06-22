<?php
declare(strict_types=1);

namespace CloudPlayDev\Tests\ConfluenceClient\HttpClient\Plugin;

use CloudPlayDev\ConfluenceClient\HttpClient\Plugin\TokenAuthentication;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Promise\HttpFulfilledPromise;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class TokenAuthenticationTest extends TestCase
{
    /**
     * @var callable
     */
    private $first;

    protected function setUp(): void
    {
        $this->first = static function () {};
    }

    public function testCanAddTokenHeader(): void
    {
        $token = 'someTestToken';
        $plugin = new TokenAuthentication($token);

        $verify = function (RequestInterface $request) use ($token) {

            static::assertTrue($request->hasHeader('Authorization'));
            static::assertSame('Bearer ' . $token, $request->getHeaderLine('Authorization'));

            return new HttpFulfilledPromise(new Response());
        };

        $request = new Request('GET', 'https://example.com');
        $plugin->handleRequest($request, $verify, $this->first);
    }
}
