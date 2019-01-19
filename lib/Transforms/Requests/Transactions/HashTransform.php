<?php

namespace StackPay\Payments\Transforms\Requests\Transactions;

trait HashTransform
{
    public function requestHash($request)
    {
        if (!$request->shouldHash()) {
            return true;
        }

        $request->appendHeaders([
            'HashMethod' => 'SHA-256',
            'Hash'       => hash('sha256', ($request->hashBody() ? $request->rawBody() : '') . $request->hashKey())
        ]);
    }
}
