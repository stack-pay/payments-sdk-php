<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;

trait ErrorTransform
{
    public function responseError($response)
    {
        if (! is_array($response->body())) {
            throw new \Exception('Response did not contain any information');
        } elseif (array_key_exists('error_code', $response->body())) {
            $exception = (new Exceptions\RequestErrorException(
                $response->body()['error_message'],
                $response->body()['error_code']
            ));

            if (array_key_exists('errors', $response->body())) {
                $exception->setErrors($response->body()['errors']);
            }

            throw $exception;
        }

        $response->setSuccessful();
    }
}
