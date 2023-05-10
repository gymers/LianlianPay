<?php

namespace Gymers\LianlianPay\Client;

use GuzzleHttp\Client as GuzzleHttpClient;

class Client
{
    public $method = 'POST';

    public $uri;

    public $headers;

    public $body;

    public function request()
    {
        $client = new GuzzleHttpClient();
        $response = $client->request(
            $this->method,
            $this->uri,
            [
                'headers' => $this->headers,
                'body' => $this->body,
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * HTTP method.
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * HTTP uri.
     *
     * @param string $uri
     *
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * HTTP headers.
     *
     * @param string $headers
     *
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * HTTP body.
     *
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }
}
