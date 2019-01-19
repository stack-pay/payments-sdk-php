<?php

namespace StackPay\Payments\Structures\Transactions;

class Response
{
    public $locked = false;
    public $raw;
    public $success;
    public $errorCode;
    public $httpCode;
    public $errorMessage;
    public $headers;
    public $body;
    public $hashKey;
    public $rawBody;
    public $shouldHash = true;

    public function lock()
    {
        $this->locked = true;
    }

    public function raw($raw = null)
    {
        if (! $this->locked) {
            if (is_null($this->raw)) {
                $this->raw = $raw;
            }

            return $this->raw;
        }
    }

    public function setError($errorCode, $errorMessage)
    {
        if (! $this->locked) {
            $this->success      = false;
            $this->errorCode    = $errorCode;
            $this->errorMessage = $errorMessage;
        }

        $this->lock();
    }

    public function setSuccessful()
    {
        if (! $this->locked) {
            $this->success = true;
        }
    }

    public function success()
    {
        return $this->success;
    }

    public function httpCode($httpCode = null)
    {
        if (! $this->locked && $httpCode) {
            $this->httpCode = $httpCode;
        }

        return $this->httpCode;
    }

    public function headers($headers = null)
    {
        if (! $this->locked && $headers) {
            $this->headers = $headers;
        }

        return $this->headers;
    }

    public function appendHeaders(array $headers)
    {
        if (! $this->locked) {
            if (! $this->headers) {
                $this->headers = [];
            }

            $this->headers = $this->headers + $headers;
        }
    }

    public function body($body = null)
    {
        if (! $this->locked && ! is_null($body)) {
            $this->body = $body;
        }

        return $this->body;
    }

    public function hashKey($hashKey = null)
    {
        if (! $this->locked && $hashKey) {
           $this->hashKey = $hashKey;
        }

        return $this->hashKey;
    }

    public function rawBody($rawBody = null)
    {
        if (! $this->locked && $rawBody) {
            $this->rawBody = $rawBody;
        }

        return $this->rawBody;
    }

    public function shouldHash($shouldHash = null)
    {
        if (! $this->locked && !is_null($shouldHash)) {
            $this->shouldHash = $shouldHash;
        }

        return $this->shouldHash;
    }
}
