<?php
declare(strict_types=1);
/**
 * This file is part of the CloudPlayDev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\ConfluenceClient;

use function is_resource;

/**
 * Class Curl
 * @package CloudPlayDev\ConfluenceClient
 */
class Curl
{
    /**
     * @var resource
     */
    private $curl;
    private string $hostUrl;

    /**
     * Class constructor
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $username, string $password)
    {
        $ch = curl_init($host);

        if(!is_resource($ch)) {
            throw new Exception('Connection could not be established.');
        }

        $this->curl = $ch;

        $this->hostUrl = $host;
        curl_setopt_array($this->curl, [
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $username . ':' . $password
        ]);
    }

    /**
     * Get host url
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->hostUrl;
    }

    /**
     * Set option to web client
     *
     * @param int $name
     * @param mixed $value
     * @return $this
     */
    public function setOption(int $name, $value): Curl
    {
        curl_setopt($this->curl, $name, $value);
        return $this;
    }

    /**
     * Set multiple options
     *
     * @param array<int, mixed> $options
     * @return $this
     */
    public function setOptions(array $options): Curl
    {
        curl_setopt_array($this->curl, $options);
        return $this;
    }

    /**
     * Execute the quest and return response from server
     * @return mixed
     */
    public function execute()
    {
        return curl_exec($this->curl);
    }

    /**
     * Set headers from an array to web client
     *
     * @param array<string, string> $headers
     * @return $this
     */
    public function setHeaders(array $headers): Curl
    {
        $httpHeaders = [];
        foreach ($headers as $key => $value) {
            $httpHeaders[] = $key . ':' . $value;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $httpHeaders);
        return $this;
    }

    /**
     * Get information of the request
     *
     * @param int $name
     * @return mixed
     */
    public function getInfo(int $name)
    {
        return curl_getinfo($this->curl, $name);
    }

    /**
     * Get errors from the request
     *
     * @return string
     */
    public function getError(): string
    {
        return curl_error($this->curl);
    }

    /**
     * Get error number
     *
     * @return int
     */
    public function getErrorNumber(): int
    {
        return curl_errno($this->curl);
    }

    /**
     * Close connection
     *
     * @return $this
     */
    public function close(): Curl
    {
        curl_close($this->curl);
        return $this;
    }
}