<?php

namespace StackPay\Payments\Transforms\Requests\Transactions;

trait AuthTransform
{
    public function requestClientAuth($request)
    {
        $request->appendHeaders(['Authorization' => 'Bearer '.$this->privateKey]);
    }

    public function requestPublicAuth($request)
    {
        $request->appendHeaders(['Authorization' => 'Bearer '.$this->publicKey]);
    }
}
