<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class ScheduledTransactionRequest extends Request
{
    public $scheduledTransaction;

    public function __construct(Structures\ScheduledTransaction $scheduledTransaction)
    {
        parent::__construct();

        $this->scheduledTransaction = $scheduledTransaction;
    }

    public function create()
    {
        $this->method   = 'POST';
        $this->endpoint = '/api/scheduled-transactions';
        $this->hashKey  = $this->scheduledTransaction->merchant->hashKey;
        $this->body     = $this->restTranslator->buildScheduledTransactionElement($this->scheduledTransaction);

        return $this;
    }

    public function delete()
    {
        $this->method   = 'DELETE';
        $this->endpoint = '/api/scheduled-transactions/'. $this->scheduledTransaction->id;
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        return $this;
    }

    public function get()
    {
        $this->method   = 'GET';
        $this->endpoint = '/api/scheduled-transactions/'. $this->scheduledTransaction->id;
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        return $this;
    }

    public function retry()
    {
        $this->method    = 'POST';
        $this->endpoint  = '/api/scheduled-transactions/'. $this->scheduledTransaction->id .'/attempts';
        $this->hashKey   = StackPay::$privateKey;
        $this->body      = null;

        if ($this->scheduledTransaction->paymentMethod) {
            $this->body = [
                'payment_method' => $this->restTranslator->buildPaymentMethodElement(
                    $this->scheduledTransaction->paymentMethod
                )
            ];
        }

        return $this;
    }

    public function getDailyScheduledTransactions()
    {
        $this->method   = 'GET';
        $this->endpoint = '/api/scheduled-transactions?createdBetween'. $this->scheduledTransaction->scheduledAt.','. $this->scheduledTransaction->scheduledAt;
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        return $this;
    }
}
