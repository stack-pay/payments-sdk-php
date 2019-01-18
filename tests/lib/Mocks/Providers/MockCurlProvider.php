<?php

namespace Test\Mocks\Providers;

use StackPay\Payments\Providers;

class MockCurlProvider extends Providers\CurlProvider implements Providers\CurlProviderInterface
{
    public $returnValues;
    public $calls;
    public $headers;

    public function __construct($returnValues)
    {
        $this->returnValues = $returnValues;
        $this->calls        = [];
        $this->headers      = [];
    }

    public function newCurl()
    {
        $this->headers = [];
    }

    public function post($url, array $body)
    {
        array_push($this->calls, [
            'URL'  => $url,
            'Body' => $body,
            'Headers' => $this->headers
        ]);

        if (count($this->calls) > $this->returnValues)
        {
            throw new Exception('Unexpected call to curl, expecting '. (count($this->calls) - 1) .' calls');
        }

        return $this->returnValues[count($this->calls)-1]['Body'];
    }

    public function get($url, array $body)
    {
        array_push($this->calls, [
            'URL'  => $url,
            'Body' => $body,
            'Headers' => $this->headers
        ]);

        if (count($this->calls) > $this->returnValues)
        {
            throw new Exception('Unexpected call to curl, expecting '. (count($this->calls) - 1) .' calls');
        }

        return $this->returnValues[count($this->calls)-1]['Body'];
    }

    public function setHeader($key, $value)
    {
        array_push($this->headers, [
            'Key'  => $key,
            'Value' => $value
        ]);
    }

    public function responseHeaders()
    {
        return $this->returnValues[count($this->calls)-1]['Headers'];
    }

    public function httpStatusCode()
    {
        return $this->returnValues[count($this->calls)-1]['StatusCode'];
    }
}
