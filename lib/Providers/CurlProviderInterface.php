<?php

namespace StackPay\Payments\Providers;

interface CurlProviderInterface
{
    public function newCurl();
    public function post($url, array $body);
    public function setHeader($key, $value);
    public function responseHeaders();
    public function httpStatusCode();
}
