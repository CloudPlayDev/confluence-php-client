<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient;

use CloudPlayDev\ConfluenceClient\Api\Content;
use CloudPlayDev\ConfluenceClient\HttpClient\Builder;
use CloudPlayDev\ConfluenceClient\HttpClient\Plugin\BasicAuthentication;
use CloudPlayDev\ConfluenceClient\HttpClient\Plugin\TokenAuthentication;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;

class ConfluenceClient
{
    /**
     * Default User-Agent send with every request
     */
    private const USER_AGENT = 'cloudplaydev-confluence-php-client/0.1.0';
    private Builder $httpClientBuilder;

    public function __construct(string $confluenceHostUrl, Builder $httpClientBuilder = null)
    {
        $this->httpClientBuilder = $httpClientBuilder ?: new Builder();
        $this->httpClientBuilder->addPlugin(new HeaderDefaultsPlugin([
            'User-Agent' => self::USER_AGENT,
        ]));

        $this->setUrl($confluenceHostUrl);
    }

    public function authenticate(string $token): ConfluenceClient
    {
        $this->httpClientBuilder->removePlugin(TokenAuthentication::class);
        $this->httpClientBuilder->addPlugin(new TokenAuthentication($token));

        return $this;
    }

    public function authenticateBasicAuth(string $username, string $password): ConfluenceClient
    {
        $this->httpClientBuilder->removePlugin(BasicAuthentication::class);
        $this->httpClientBuilder->addPlugin(new BasicAuthentication($username, $password));

        return $this;
    }

    public function content(): Content
    {
        return new Content($this);
    }

    /**
     * Get the HTTP client.
     *
     * @return HttpMethodsClientInterface
     */
    public function getHttpClient(): HttpMethodsClientInterface
    {
        return $this->httpClientBuilder->getHttpClient();
    }

    public function setUrl(string $url): void
    {
        $uri = $this->httpClientBuilder->getUriFactory()->createUri($url);

        $this->httpClientBuilder->removePlugin(AddHostPlugin::class);
        $this->httpClientBuilder->addPlugin(new AddHostPlugin($uri));
    }


}
