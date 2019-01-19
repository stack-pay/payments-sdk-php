<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Subscription implements Interfaces\Subscription
{
    public $id;
    public $paymentMethod;
    public $paymentPlan;
    public $externalID;
    public $amount;
    public $splitAmount;
    public $initialTransaction;
    public $scheduledTransactions;

    public function id()
    {
        return $this->$id;
    }
    
    public function paymentMethod()
    {
        return $this->$paymentMethod;
    }

    public function externalID()
    {
        return $this->$externalID;
    }

    public function amount()
    {
        return $this->$amount;
    }

    public function splitAmount()
    {
        return $this->$splitAmount;
    }

    public function initialTransaction()
    {
        return $this->$initialTransaction;
    }

    public function scheduledTransactions()
    {
        return $this->$scheduledTransactions;
    }

    // --------

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod = null)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function setExternalID($externalID = null)
    {
        $this->externalID = $externalID;

        return $this;
    }

    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    public function setSplitAmount($splitAmount = null)
    {
        $this->splitAmount = $splitAmount;

        return $this;
    }

    public function setInitialTransaction(Transaction $initialTransaction = null)
    {
        $this->initialTransaction = $initialTransaction;

        return $this;
    }

    public function setScheduledTransactions($scheduledTransactions = null)
    {
        $this->scheduledTransactions = $scheduledTransactions;

        return $this;
    }
}
