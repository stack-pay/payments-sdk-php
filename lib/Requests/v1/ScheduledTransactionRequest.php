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
        $this->endpoint = '/api/scheduled-transactions?createdBetween='. $this->paginatedScheduledTransaction->beforeDate.','. $this->paginatedScheduledTransaction->afterDate;
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        if ($this->paginatedScheduledTransaction->status) {
            $this->endpoint .= '&status='. $this->scheduledTransaction->status;
        }
        if ($this->paginatedScheduledTransaction->perPage) {
            $this->endpoint .= '&per_page='. $this->scheduledTransaction->perPage;
        }
        if ($this->paginatedScheduledTransaction->currentPage) {
            $this->endpoint .= '&page='. $this->scheduledTransaction->currentPage;
        }

        return $this;
    }
}
