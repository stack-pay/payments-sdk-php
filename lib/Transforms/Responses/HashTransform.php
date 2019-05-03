<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;

trait HashTransform
{
    public function responseHash($response)
    {
        if (!$response->shouldHash()) {
            return true;
        }

        // var_dump($response->headers());
        if (! array_key_exists('HashMethod', $response->headers()) ||
            ! array_key_exists('Hash', $response->headers())
        ) {
            throw new Exceptions\HashValidationException;
        }

        if (hash('sha256', $response->rawBody().$response->hashKey()) != $response->headers()['Hash']) {
            throw new Exceptions\HashValidationException;
        }

        return true;
    }
}
