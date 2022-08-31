<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

final class BasicAuthentication implements Plugin
{
    private string $username;
    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $auth = base64_encode("$this->username:$this->password");
        return $next($request->withHeader('Authorization', sprintf('Basic %s', $auth)));
    }
}
