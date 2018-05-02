<?php

namespace StackPay\Payments\Transforms\Responses;

trait CaptureTransform
{
    public function responseCapture($transaction)
    {
        $this->responseAuth($transaction);

        $body = $transaction->response()->body();

        if (array_key_exists('CapturedTransaction', $body['Payment'])) {
            $transaction->object()->createCapturedTransaction()->setID(
                $body['Payment']['CapturedTransaction']
            );
        }
    }
}
