<?php

namespace StackPay\Payments\Interfaces;

interface Refund extends Transaction
{
    public function originalTransaction();
    public function amount();
    public function split();
    public function currency();
    public function refundedTransaction();

    // --------

    public function setOriginalTransaction(Transaction $originalTransaction = null);
    public function setAmount($amount = null);
    public function setSplit(Split $split = null);
    public function setRefundedTransaction(Transaction $refundedTransaction = null);

    // --------------

    public function createSplit();
    public function createRefundedTransaction();
}
