<?php

namespace StackPay\Payments\Interfaces;

interface PaginatedPaymentPlans extends Paginated
{
    public function merchant();
    public function plans();

    // ---------

    public function setMerchant(Merchant $plans = null);
    public function setPlan(array $plans = null);
}
