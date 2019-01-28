<?php

namespace StackPay\Payments\Interfaces;

interface PaginatedScheduledTransactions extends Paginated
{
    public function merchant();
    public function beforeDate();
    public function afterDate();
    public function scheduledTransactions();

    // ---------

    public function setMerchant(Merchant $merchant = null);
    public function setBeforeDate(\DateTime $date = null);
    public function setAfterDate(\DateTime $date = null);
    public function setScheduledTransactions(array $scheduledTransactions = null);
}
