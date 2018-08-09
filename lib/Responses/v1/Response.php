<?php

namespace StackPay\Payments\Responses\v1;

use GuzzleHttp\Psr7\Response as HttpResponse;

use StackPay\Payments\Exceptions;
use StackPay\Payments\StackPay;
use StackPay\Payments\Requests\v1\Request;

class Response
{
    /**
     * @var \StackPay\Payments\Requests\v1\Request
     */
    protected $request;

    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    protected $http_response;

    /**
     * @var boolean
     */
    protected $success;

    /**
     * @var \stdClass
     */
    protected $payload;

    /**
     * @var \stdClass
     */
    protected $body;

    /**
     * @var \StackPay\Payments\Responses\v1\ErrorResponse
     */
    protected $error;

    /**
     * Instantiate a Response object and handle the API response.
     */
    public function __construct(Request $request, HttpResponse $http_response)
    {
        $this->request          = $request;
        $this->http_response    = $http_response;
        $this->payload          = json_decode($this->http_response->getBody());

        $this->body             = property_exists($this->payload, 'Body')
            ? $this->payload->Body
            : $this->payload;

        if (! empty($this->body->error_code)) {
            $this->handleError();
        } else {
            $this->handleSuccess();
        }
    }

    protected function transformHeaders()
    {
        if (property_exists($this->payload, 'Header')) {
            foreach (get_object_vars($this->payload->Header) as $key => $value) {
                if ($key == 'Security') {
                    $this->http_response = $this->http_response
                        ->withAddedHeader('HashMethod', $value->HashMethod)
                        ->withAddedHeader('Hash', $value->Hash);
                } else {
                    $this->http_response = $this->http_response->withAddedHeader($key, $value);
                }
            }
        }
    }

    protected function validateSecurityHash()
    {
        if (! $this->http_response->hasHeader('HashMethod') ||
            ! $this->http_response->hasHeader('Hash')
        ) {
            throw new Exceptions\HashValidationException;
        }

        $check_hash = hash('sha256', $this->jsonTransform($this->body) . $this->request->getHashKey());

        if ($check_hash != $this->http_response->getHeader('Hash')[0]) {
            throw new Exceptions\HashValidationException;
        }
    }

    protected function handleSuccess()
    {
        $this->transformHeaders();

        $this->validateSecurityHash();

        $this->success = true;
    }

    protected function handleError()
    {
        $this->success = false;

        $this->error = new ErrorResponse(
            $this->body->error_code,
            $this->body->error_message,
            property_exists($this->body, 'errors')
                ? $this->body->errors
                : null
        );
    }

    public function body()
    {
        return $this->body;
    }

    public function payload()
    {
        return $this->payload;
    }

    public function error()
    {
        return $this->error;
    }

    public function success()
    {
        return $this->success;
    }

    public function status()
    {
        return $this->http_response->getStatusCode();
    }

    protected function jsonTransform($body = null)
    {
        return json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
