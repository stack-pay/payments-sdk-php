<?php

namespace StackPay\Payments\Transforms\Requests\Transactions;

trait HashTransform
{
    public function requestHash($request)
    {
        $request->appendHeaders([
            'HashMethod' => 'SHA-256',
            'Hash'       => hash('sha256', $request->rawBody().$request->hashKey())
        ]);
    }
}
