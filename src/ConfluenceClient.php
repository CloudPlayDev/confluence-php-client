<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient;

use CloudPlayDev\ConfluenceClient\Api\Content;
use CloudPlayDev\ConfluenceClient\HttpClient\Builder;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\AddPathPlugin;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Message\Authentication\BasicAuth;
use Http\Message\Authentication\Bearer;

class ConfluenceClient
{
    /**
     * Default User-Agent send with every request
     * Use protected visibility to facilitate extension
     * @see http://fabien.potencier.org/pragmatism-over-theory-protected-vs-private.html
     */
    protected const USER_AGENT = 'cloudplaydev-confluence-php-client/0.1.0';

    /**
     * Use protected visibility to facilitate extension
     * @see http://fabien.potencier.org/pragmatism-over-theory-protected-vs-private.html
     */
    protected Builder $httpClientBuilder;

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
        $this->httpClientBuilder->removePlugin(AuthenticationPlugin::class);
        $this->httpClientBuilder->addPlugin(new AuthenticationPlugin(new Bearer($token)));

        return $this;
    }

    public function authenticateBasicAuth(string $username, string $password): ConfluenceClient
    {
        $this->httpClientBuilder->removePlugin(AuthenticationPlugin::class);
        $this->httpClientBuilder->addPlugin(new AuthenticationPlugin(new BasicAuth($username, $password)));

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

    /**
     * Register basic properties about original URI in order to
     * restore them on every request
     *
     * @param string $url
     * @return void
     */
    public function setUrl(string $url): void
    {
        $uri = $this->httpClientBuilder->getUriFactory()->createUri($url);

        $this->httpClientBuilder->removePlugin(AddHostPlugin::class);
        $this->httpClientBuilder->addPlugin(new AddHostPlugin($uri));

        // Prepend path if any
        if ($uri->getPath()) {
            $this->httpClientBuilder->removePlugin(AddPathPlugin::class);
            $this->httpClientBuilder->addPlugin(new AddPathPlugin($uri));
        }

        // Report userInfo as Basic authentication
        $userInfo = $uri->getUserInfo();
        if (!empty($userInfo) && str_contains($userInfo, ':')) {
            $this->httpClientBuilder->addPlugin(new AuthenticationPlugin(new BasicAuth(...explode(':', $userInfo, 2))));
        }
    }
}
