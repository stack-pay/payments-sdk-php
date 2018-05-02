<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Rate implements Interfaces\Rate
{
    public $feeRate;
    public $feeTransaction;
    public $feeNotes;
    public $name;

    public function feeRate()
    {
        return $this->feeRate;
    }

    public function feeTransaction()
    {
        return $this->feeTransaction;
    }

    public function feeNotes()
    {
        return $this->feeNotes;
    }

    public function name()
    {
        return $this->name;
    }

    // ----

    public function setFeeRate($feeRate = null)
    {
        $this->feeRate = $feeRate;

        return $this;
    }

    public function setFeeTransaction($feeTransaction = null)
    {
        $this->feeTransaction = $feeTransaction;

        return $this;
    }

    public function setFeeNotes($feeNotes = null)
    {
        $this->feeNotes = $feeNotes;

        return $this;
    }

    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }
}
