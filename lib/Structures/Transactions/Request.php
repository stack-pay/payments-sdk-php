<?php

namespace StackPay\Payments\Structures\Transactions;

class Request
{
    public $locked = false;

    public $headers;
    public $body;
    public $rawBody;
    public $raw;
    public $hashKey;
    public $endpoint;

    public function lock()
    {
        $this->locked = true;

        return $this->locked;
    }

    public function headers($headers = null)
    {
        if (! $this->locked && $headers) {
            $this->headers = $headers;
        }

        return $this->headers;
    }

    public function appendHeaders($headers = null)
    {
        if (! $this->locked && $headers) {
            if (! $this->headers) {
                $this->headers = [];
            }

            $this->headers = $this->headers + $headers;
        }

        return $this->headers;
    }

    public function body($body = null)
    {
        if (! $this->locked && $body) {
            $this->body = $body;
        }

        return $this->body;
    }

    public function raw($raw = null)
    {
        if (! $this->locked && $raw) {
            $this->raw = $raw;
        }

        return $this->raw;
    }

    public function rawBody($rawBody = null)
    {
        if (! $this->rawBody && $rawBody) {
            $this->rawBody = $rawBody;
        }

        return $this->rawBody;
    }

    public function hashKey($hashKey = null)
    {
        if (! $this->hashKey && $hashKey) {
            $this->hashKey = $hashKey;
        }

        return $this->hashKey;
    }

    public function endpoint($endpoint = null)
    {
        if (! $this->endpoint && $endpoint) {
            $this->endpoint = $endpoint;
        }

        return $this->endpoint;
    }
}
