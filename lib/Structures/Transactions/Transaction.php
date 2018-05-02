<?php

namespace StackPay\Payments\Structures\Transactions;

class Transaction
{
    protected $locked;

    protected $request;
    protected $response;
    protected $object;
    protected $client;
    protected $metadata;

    public function __construct($object)
    {
        $this->locked   = false;
        $this->request  = new Request();
        $this->response = new Response();
        $this->object   = $object;
    }

    public function lock()
    {
        $this->locked = true;

        return $this->locked;
    }

    public function request($request = null)
    {
        if (! $this->locked && $request) {
            $this->request = $request;
        }

        return $this->request;
    }

    public function response($response = null)
    {
        if (! $this->locked && $response) {
            $this->response = $response;
        }

        return $this->response;
    }

    public function object($object = null)
    {
        if (! $this->locked && $object) {
            $this->object = $object;
        }

        return $this->object;
    }

    public function client($client = null)
    {
        if (! $this->locked && $client) {
            $this->client = $client;
        }

        return $this->client;
    }

    public function metadata($metadata = null)
    {
        if (! $this->locked && $metadata) {
            $this->response = $metadata;
        }

        return $this->metadata;
    }

    public function idempotencyKey($idempotencyKey = null)
    {
        if (! $this->locked && $idempotencyKey) {
            $this->idempotencyKey = $idempotencyKey;
        }

        return $this->idempotencyKey;
    }
}
