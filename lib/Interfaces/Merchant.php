<?php

namespace StackPay\Payments\Interfaces;

interface Merchant
{
    public function id();
    public function hashKey();
    public function creditCardTransactionLimit();
    public function creditCardMonthlyLimit();
    public function creditCardCurrentVolume();
    public function achTransactionLimit();
    public function achMonthlyLimit();
    public function achCurrentVolume();
    public function rates();
    public function rate();
    public function link();
    public function externalID();
    public function hostedPageAccessToken();

    //-----------

    public function setID($id = null);
    public function setHashKey($hashKey = null);
    public function setCreditCardTransactionLimit($creditCardTransactionLimit = null);
    public function setCreditCardMonthlyLimit($creditCardMonthlyLimit = null);
    public function setCreditCardCurrentVolume($creditCardCurrentVolume = null);
    public function setACHTransactionLimit($achTransactionLimit = null);
    public function setACHMonthlyLimit($achMonthlyLimit = null);
    public function setACHCurrentVolume($achCurrentVolume = null);
    public function setRates($rates = null);
    public function setRate(Rate $rate = null);
    public function setLink($rate = null);
    public function setExternalID($externalID = null);
    public function setHostedPageAccessToken($hostedPageAccessToken = null);

    //-------

    public function appendRate();
}
