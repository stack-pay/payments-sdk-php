<?php

namespace StackPay\Payments\Interfaces;

interface Rate
{
    public function bankFeeRate();
    public function bankFeeTransaction();
    public function bankFeeNotes();
    public function cardFeeRate();
    public function cardFeeTransaction();
    public function cardFeeNotes();
    public function name();

    // ----

    public function setBankFeeRate($bankFeeRate = null);
    public function setBankFeeTransaction($bankFeeRate = null);
    public function setBankFeeNotes($bankFeeRate = null);
    public function setCardFeeRate($cardFeeRate = null);
    public function setCardFeeTransaction($cardFeeRate = null);
    public function setCardFeeNotes($cardFeeRate = null);
    public function setName($name = null);
}
