<?php

namespace StackPay\Payments\Interfaces;

interface VoidTransaction extends Transaction
{
    public function originalTransaction();
    public function voidedTransaction();

    // --------

    public function setOriginalTransaction(Transaction $originalTransaction = null);
    public function setVoidedTransaction(Transaction $voidedTransaction = null);

    // --------

    public function createVoidedTransaction();
}
