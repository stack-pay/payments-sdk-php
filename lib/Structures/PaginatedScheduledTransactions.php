<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class PaginatedScheduledTransactions implements Interfaces\PaginatedScheduledTransaction
{
    public $merchant;
    public $beforeDate;
    public $afterDate;
    public $scheduledTransactions;
    public $total;
    public $count;
    public $perPage;
    public $currentPage;
    public $totalPages;
    public $links;

    public function merchant()
    {
        return $this->merchant;
    }

    public function beforeDate()
    {
        return $this->beforeDate;
    }

    public function afterDate()
    {
        return $this->afterDate;
    }

    public function scheduledTransactions()
    {
        return $this->scheduledTransactions;
    }

    public function total()
    {
        return $this->total;
    }

    public function count()
    {
        return $this->count;
    }

    public function perPage()
    {
        return $this->perPage;
    }

    public function currentPage()
    {
        return $this->currentPage;
    }

    public function totalPages()
    {
        return $this->totalPages;
    }

    public function links()
    {
        return $this->links;
    }

    // ---------

    public function setMerchant(Interfaces\Merchant $merchant = null)
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function setBeforeDate($beforeDate = null)
    {
        $this->beforeDate = $beforeDate;

        return $this;
    }

    public function setAfterDate($afterDate = null)
    {
        $this->afterDate = $afterDate;

        return $this;
    }

    public function setScheduledTransactions(array $scheduledTransactions = null)
    {
        $this->scheduledTransactions = $scheduledTransactions;

        return $this;
    }

    public function setTotal($total = null)
    {
        $this->total = $total;

        return $this;
    }

    public function setCount($count = null)
    {
        $this->count = $count;

        return $this;
    }

    public function setPerPage($perPage = null)
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function setCurrentPage($currentPage = null)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function setTotalPages($totalPages = null)
    {
        $this->totalPages = $totalPages;

        return $this;
    }

    public function setLinks(array $links = null)
    {
        $this->links = $links;

        return $this;
    }    
}
