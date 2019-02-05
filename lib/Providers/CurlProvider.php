<?php

namespace StackPay\Payments\Providers;

use Curl\Curl;

interface CurlProviderInterface
{
    public function newCurl();
    public function post($url, array $body);
    public function setHeader($key, $value);
    public function responseHeaders();
    public function httpStatusCode();
}

class CurlProvider implements CurlProviderInterface
{
    private $curl;

    public function newCurl()
    {
        $this->curl = new Curl();

        // Avoid decoding a response by returning raw response
        $decoder = function ($response) {
            return $response;
        };

        $this->curl->setTimeout(60); // default timeout of 30 seconds is too short
        $this->curl->setJsonDecoder($decoder);
        $this->curl->setXmlDecoder($decoder);
    }

    public function post($url, array $body)
    {
        return $this->curl->post($url, $body);
    }
    
    public function put($url, array $body)
    {
        return $this->curl->put($url, $body);
    }

    public function get($url, array $body)
    {
        return $this->curl->get($url, $body);
    }

    public function delete($url)
    {
        return $this->curl->delete($url);
    }

    public function setHeader($key, $value)
    {
        $this->curl->setHeader($key, $value);
    }

    public function responseHeaders()
    {
        return $this->curl->response_headers;
    }

    public function httpStatusCode()
    {
        return $this->curl->httpStatusCode;
    }
}
