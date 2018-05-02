<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Refund extends Reversal
{
    public $type = 'Refund';
    public $originalTransaction;
    public $refundedTransaction;

    public function type()
    {
        return 'Refund';
    }

    public function refundedTransaction()
    {
        return $this->refundedTransaction;
    }

    public function originalTransaction()
    {
        return $this->originalTransaction;
    }

    // --------

    public function setOriginalTransaction(Interfaces\Transaction $originalTransaction = null)
    {
        $this->originalTransaction = $originalTransaction;

        return $this;
    }

    public function setRefundedTransaction(Interfaces\Transaction $refundedTransaction = null)
    {
        $this->refundedTransaction = $refundedTransaction;

        return $this;
    }

    // --------------

    public function createOriginalTransaction()
    {
        if (! $this->originalTransaction) {
            $this->originalTransaction = new Transaction();
        }

        return $this->originalTransaction;
    }

    public function createRefundedTransaction()
    {
        if (! $this->refundedTransaction) {
            $this->refundedTransaction = new Transaction();
        }

        return $this->refundedTransaction;
    }
}
