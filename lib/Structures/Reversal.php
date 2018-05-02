<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

abstract class Reversal extends Transaction implements Interfaces\Refund, Interfaces\VoidTransaction
{
    public $originalTransaction;
    public $refundedTransaction;
    public $voidedTransaction;
    public $amount;
    public $currency;
    public $split;

    public function amount()
    {
        return $this->amount;
    }

    public function split()
    {
        return $this->split;
    }

    public function currency()
    {
        return $this->currency;
    }

    public function originalTransaction()
    {
        return $this->originalTransaction;
    }

    public function refundedTransaction()
    {
        return $this->refundedTransaction;
    }

    public function voidedTransaction()
    {
        return $this->voidedTransaction;
    }

    // --------

    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    public function setSplit(Interfaces\Split $split = null)
    {
        $this->split = $split;

        return $this;
    }

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

    public function setVoidedTransaction(Interfaces\Transaction $voidedTransaction = null)
    {
        $this->voidedTransaction = $voidedTransaction;

        return $this;
    }

    // --------------

    public function createSplit()
    {
        if (! $this->split) {
            $this->split = new Split();
        }

        return $this->split;
    }

    public function createRefundedTransaction()
    {
        if (! $this->refundedTransaction) {
            $this->refundedTransaction = new Transaction();
        }

        return $this->refundedTransaction;
    }

    public function createVoidedTransaction() // Refunds are occasionally processed as Voids
    {
        if (! $this->voidedTransaction) {
            $this->voidedTransaction = new Transaction();
        }

        return $this->voidedTransaction;
    }
}
