<?php

namespace StackPay\Payments\Interfaces;

interface PaginatedPaymentPlans extends Paginated
{
    public function merchant();
    public function plans();

    // ---------

    public function setMerchant(Merchant $merchant = null);
    public function setPlans(array $plans = null);
}
