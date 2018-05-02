<?php

namespace StackPay\Payments\Interfaces;

interface Rate
{
    public function feeRate();
    public function feeTransaction();
    public function feeNotes();
    public function name();

    // ----

    public function setFeeRate($feeRate = null);
    public function setFeeTransaction($feeRate = null);
    public function setFeeNotes($feeRate = null);
    public function setName($name = null);
}
