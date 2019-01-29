<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class PaginatedScheduledTransactionRequest extends Request
{
    public $paginatedScheduledTransaction;

    public function __construct(Structures\PaginatedScheduledTransactions $paginatedScheduledTransaction)
    {
        parent::__construct();

        $this->paginatedScheduledTransaction = $paginatedScheduledTransaction;
    }

    public function getDailyScheduledTransactions()
    {
        $this->method   = 'GET';
        $this->endpoint = '/api/scheduled-transactions?createdBetween='. $this->paginatedScheduledTransaction->beforeDate->format('Y-m-d').','. $this->paginatedScheduledTransaction->afterDate->format('Y-m-d');
        $this->hashKey  = StackPay::$privateKey;
        $this->body     = null;

        if ($this->paginatedScheduledTransaction->status) {
            $this->endpoint .= '&status='. $this->paginatedScheduledTransaction->status;
        }
        if ($this->paginatedScheduledTransaction->perPage) {
            $this->endpoint .= '&per_page='. $this->paginatedScheduledTransaction->perPage;
        }
        if ($this->paginatedScheduledTransaction->currentPage) {
            $this->endpoint .= '&page='. $this->paginatedScheduledTransaction->currentPage;
        }

        return $this;
    }
}
