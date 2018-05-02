<?php

namespace StackPay\Payments\Transforms\Requests\Transactions;

trait IdempotencyTransform
{
    public function requestIdempotency($transaction)
    {
        if ($transaction->idempotencyKey()) {
            $transaction->request()->appendHeaders([
                'IdempotencyKey' => $transaction->idempotencyKey()
            ]);
        }
    }
}
