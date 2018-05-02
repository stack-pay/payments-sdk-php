<?php

namespace StackPay\Payments\Transforms\Requests\Transactions;

trait V1Transform
{
    public function requestV1($request)
    {
        $body = [
            'Body' => $request->body(),
        ];

        $headers = [];

        foreach ($request->headers() as $key => $value) {
            switch ($key) {
                case 'Application':
                    $body['Header']['Application'] = $value;
                    break;

                case 'ApiVersion':
                    $body['Header']['ApiVersion'] = $value;
                    break;

                case 'Mode':
                    $body['Header']['Mode'] = $value;
                    break;

                case 'HashMethod':
                    $body['Header']['Security']['HashMethod'] = $value;
                    break;

                case 'Hash':
                    $body['Header']['Security']['Hash'] = $value;
                    break;

                case 'IdempotencyKey':
                    $body['Header']['IdempotencyKey'] = $value;
                    break;

                default:
                    $headers[$key] = $value;
            }
        }

        $request->body($body);

        $request->headers($headers);
    }
}
