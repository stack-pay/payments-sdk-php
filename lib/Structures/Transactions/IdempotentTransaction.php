<?php

namespace StackPay\Payments\Structures\Transactions;

class IdempotentTransaction extends Transaction
{
    protected $idempotencyKey;

    public function idempotencyKey($idempotencyKey = null)
    {
        if (! $this->locked && $idempotencyKey) {
            $this->idempotencyKey = $idempotencyKey;
        }

        return $this->idempotencyKey;
    }
}
