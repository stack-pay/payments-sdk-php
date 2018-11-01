<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Merchant implements Interfaces\Merchant
{
    public $id;
    public $hashKey;
    public $creditCardTransactionLimit;
    public $creditCardMonthlyLimit;
    public $creditCardCurrentVolume;
    public $achTransactionLimit;
    public $achMonthlyLimit;
    public $achCurrentVolume;
    public $rates;
    public $rate;
    public $link;
    public $externalID;
    public $applicationName;

    public function __construct($id = null, $request_hash_key = null)
    {
        $this->setID($id);
        $this->setHashKey($request_hash_key);
    }

    public function id()
    {
        return $this->id;
    }

    public function hashKey()
    {
        return $this->hashKey;
    }

    public function creditCardTransactionLimit()
    {
        return $this->creditCardTransactionLimit;
    }

    public function creditCardMonthlyLimit()
    {
        return $this->creditCardMonthlyLimit;
    }

    public function creditCardCurrentVolume()
    {
        return $this->creditCardCurrentVolume;
    }

    public function achTransactionLimit()
    {
        return $this->achTransactionLimit;
    }

    public function achMonthlyLimit()
    {
        return $this->achMonthlyLimit;
    }

    public function achCurrentVolume()
    {
        return $this->achCurrentVolume;
    }

    public function rates()
    {
        return $this->rates;
    }

    public function rate()
    {
        return $this->rate;
    }

    public function link()
    {
        return $this->link;
    }

    public function externalID()
    {
        return $this->externalID;
    }

    public function applicationName()
    {
        return $this->applicationName;
    }

    //-----------

    public function setID($id = null)
    {
        $this->id = $id;
        return $this;
    }

    public function setHashKey($hashKey = null)
    {
        $this->hashKey = $hashKey;

        return $this;
    }

    public function setCreditCardTransactionLimit($creditCardTransactionLimit = null)
    {
        $this->creditCardTransactionLimit = $creditCardTransactionLimit;

        return $this;
    }

    public function setCreditCardMonthlyLimit($creditCardMonthlyLimit = null)
    {
        $this->creditCardMonthlyLimit = $creditCardMonthlyLimit;

        return $this;
    }

    public function setCreditCardCurrentVolume($creditCardCurrentVolume = null)
    {
        $this->creditCardCurrentVolume = $creditCardCurrentVolume;

        return $this;
    }

    public function setACHTransactionLimit($achTransactionLimit = null)
    {
        $this->achTransactionLimit = $achTransactionLimit;

        return $this;
    }

    public function setACHMonthlyLimit($achMonthlyLimit = null)
    {
        $this->achMonthlyLimit = $achMonthlyLimit;

        return $this;
    }

    public function setACHCurrentVolume($achCurrentVolume = null)
    {
        $this->achCurrentVolume = $achCurrentVolume;

        return $this;
    }

    public function setRates($rates = null)
    {
        $this->rates = $rates;

        return $this;
    }

    public function setRate(Interfaces\Rate $rate = null)
    {
        $this->rate = $rate;

        return $this;
    }

    public function setLink($link = null)
    {
        $this->link = $link;

        return $this;
    }

    public function setExternalID($externalID = null)
    {
        $this->externalID = $externalID;

        return $this;
    }

    public function setApplicationName($applicationName = null)
    {
        $this->applicationName = $applicationName;

        return $this;
    }

    // ----

    public function appendRate()
    {
        if (! $this->rates) {
            $this->rates = [];
        }

        array_push($this->rates, new Rate());

        return end($this->rates);
    }
}
