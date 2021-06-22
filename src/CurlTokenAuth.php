<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient;


class CurlTokenAuth extends Curl
{

    public function __construct(string $host, string $token)
    {
        parent::__construct($host, '', '');

        $this->setOptions([
            CURLOPT_HTTPAUTH => CURLAUTH_BEARER,
            CURLOPT_XOAUTH2_BEARER => $token,
            CURLOPT_USERPWD => null
        ]);
    }
}