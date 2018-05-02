<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class VoidTransaction extends Reversal
{
    public $type = 'Void';

    public function type()
    {
        return 'Void';
    }

    public function originalTransaction()
    {
        return $this->originalTransaction;
    }

    public function voidedTransaction()
    {
        return $this->voidedTransaction;
    }

    // --------

    public function setOriginalTransaction(Interfaces\Transaction $originalTransaction = null)
    {
        $this->originalTransaction = $originalTransaction;

        return $this;
    }

    public function setVoidedTransaction(Interfaces\Transaction $voidedTransaction = null)
    {
        $this->voidedTransaction = $voidedTransaction;

        return $this;
    }

    // ------------

    public function createOriginalTransaction()
    {
        if (! $this->originalTransaction) {
            $this->originalTransaction = new Transaction();
        }

        return $this->originalTransaction;
    }

    public function createVoidedTransaction()
    {
        if (! $this->voidedTransaction) {
            $this->voidedTransaction = new Transaction();
        }

        return $this->voidedTransaction;
    }
}
