<?php

namespace StackPay\Payments\Interfaces;

interface MerchantPaymentPlans extends PagedMetaData
{
    public function merchant();

    //-----------

    public function setMerchant(Merchant $merchant = null);
}
