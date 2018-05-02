<?php

namespace StackPay\Payments\Interfaces;

interface Capture extends Transaction
{
    public function originalTransaction();
    public function capturedTransaction();

    // -----

    public function setOriginalTransaction(Transaction $originalTransaction = null);
    public function setCapturedTransaction(Auth $capturedTransaction = null);

    // -----

    public function createCapturedTransaction();
    public function createOriginalTransaction();
}
