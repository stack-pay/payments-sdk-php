<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\Responses\v1\Response;
use StackPay\Payments\StackPay;
use StackPay\Payments\Translators;

class Request
{
    public $method;
    public $endpoint;
    public $hashKey;
    public $body;

    public $headers;

    public $response;

    public function __construct()
    {
        $this->translator       = new Translators\V1Translator;
        $this->restTranslator   = new Translators\V1RESTTranslator;
    }

    public function send()
    {
        $payload_body = $this->jsonTransform($this->body);

        $this->headers = [
            'Authorization' => 'Bearer '. StackPay::$privateKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'ApiVersion'    => 'v1',
            'HashMethod'    => 'SHA-256',
            'Hash'          => hash('sha256', $payload_body . $this->getHashKey()),
            'Mode'          => StackPay::$mode,
        ];

        // Guzzle
        $http_response = StackPay::$httpClient->request(
            $this->method,
            $this->getHostname() . $this->endpoint,
            [
                'http_errors'   => false,
                'headers'       => $this->headers,
                'body'          => $this->jsonTransform(['Body' => $this->body]),
            ]
        );

        // validate and return Response
        return new Response($this, $http_response);
    }

    public function getHashKey()
    {
        return $this->hashKey ?: StackPay::$privateKey;
    }

    protected function getHostname()
    {
        switch (StackPay::$mode) {
            case 'production':
                $hostname = 'https://api.mystackpay.com';
                break;

            case 'development':
            default:
                $hostname = 'https://sandbox-api.mystackpay.com';
                break;
        }

        return $hostname;
    }

    protected function jsonTransform($body = null)
    {
        return json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
