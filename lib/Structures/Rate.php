<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Rate implements Interfaces\Rate
{
    public $bankFeeRate;
    public $bankFeeTransaction;
    public $bankFeeNotes;
    public $cardFeeRate;
    public $cardFeeTransaction;
    public $cardFeeNotes;
    public $name;

    public function bankFeeRate()
    {
        return $this->bankFeeRate;
    }

    public function bankFeeTransaction()
    {
        return $this->bankFeeTransaction;
    }

    public function bankFeeNotes()
    {
        return $this->bankFeeNotes;
    }

    public function cardFeeRate()
    {
        return $this->cardFeeRate;
    }

    public function cardFeeTransaction()
    {
        return $this->cardFeeTransaction;
    }

    public function cardFeeNotes()
    {
        return $this->cardFeeNotes;
    }

    public function name()
    {
        return $this->name;
    }

    // ----

    public function setBankFeeRate($bankFeeRate = null)
    {
        $this->bankFeeRate = $bankFeeRate;

        return $this;
    }

    public function setBankFeeTransaction($bankFeeTransaction = null)
    {
        $this->bankFeeTransaction = $bankFeeTransaction;

        return $this;
    }

    public function setBankFeeNotes($bankFeeNotes = null)
    {
        $this->bankFeeNotes = $bankFeeNotes;

        return $this;
    }

    public function setCardFeeRate($cardFeeRate = null)
    {
        $this->cardFeeRate = $cardFeeRate;

        return $this;
    }

    public function setCardFeeTransaction($cardFeeTransaction = null)
    {
        $this->cardFeeTransaction = $cardFeeTransaction;

        return $this;
    }

    public function setCardFeeNotes($cardFeeNotes = null)
    {
        $this->cardFeeNotes = $cardFeeNotes;

        return $this;
    }

    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }
}
