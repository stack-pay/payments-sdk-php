<?php

namespace StackPay\Payments\Transforms\Requests\Transactions;

trait HeaderTransform
{
    public function requestHeaders($request)
    {
        $request->appendHeaders([
            'Application' => $this->application,
            'ApiVersion'  => $this->apiVersion,
            'Mode'        => $this->mode
        ]);
    }
}
