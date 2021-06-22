<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\HttpClient\Plugin;


use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use function sprintf;

final class TokenAuthentication implements Plugin
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        return $next($request->withHeader('Authorization', sprintf('Bearer %s', $this->token)));
    }
}