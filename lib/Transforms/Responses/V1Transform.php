<?php

namespace StackPay\Payments\Transforms\Responses;

trait V1Transform
{
    public function responseV1($response)
    {
        if (array_key_exists('Header', $response->body())) {
            foreach ($response->body()['Header'] as $key => $value) {
                switch ($key) {
                    case 'Security':
                        $response->appendHeaders($value);
                        break;

                    default:
                        $response->appendHeaders([$key => $value]);
                        break;
                }
            }
        }

        if (array_key_exists('Body', $response->body())) {
            $response->body($response->body()['Body']);
        }
    }
}
