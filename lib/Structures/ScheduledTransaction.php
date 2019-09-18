<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Currency;
use StackPay\Payments\Interfaces;

class ScheduledTransaction extends Auth implements Interfaces\ScheduledTransaction
{
    // standard
    public $id;
    public $externalId;
    public $merchant;
    public $paymentMethod;
    public $amount;
    public $status;
    public $currencyCode;
    public $scheduledAt;
    public $subscriptionId;
    public $softDescriptor;

    // V1Translator
    public $split;

    // V1RESTTranslator
    public $splitMerchant;
    public $splitAmount;

    public function id()
    {
        return $this->id;
    }

    public function externalId()
    {
        return $this->externalId;
    }

    public function merchant()
    {
        return $this->merchant;
    }

    public function paymentMethod()
    {
        return $this->paymentMethod;
    }

    public function amount()
    {
        return $this->amount;
    }

    public function status()
    {
        return $this->status;
    }

    public function currencyCode()
    {
        return $this->currencyCode;
    }

    public function scheduledAt()
    {
        return $this->scheduledAt;
    }

    public function subscriptionId()
    {
        return $this->subscriptionId;
    }

    public function softDescriptor()
    {
        return $this->softDescriptor;
    }

    public function split()
    {
        return $this->split;
    }

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }

    public function setExternalId($externalId = null)
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function setMerchant(Interfaces\Merchant $merchant = null)
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function setPaymentMethod(Interfaces\PaymentMethod $paymentMethod = null)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    public function setScheduledAt(\DateTime $date = null)
    {
        $this->scheduledAt = $date;

        return $this;
    }

    public function setCurrencyCode($currencyCode = null)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function setSubscriptionId($subscriptionId = null)
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    public function setSoftDescriptor($softDescriptor = null)
    {
        $this->softDescriptor = $softDescriptor;

        return $this;
    }

    public function setSplit(Interfaces\Split $split = null)
    {
        $this->split = $split;

        return $this;
    }

    public function createMerchant()
    {
        if (! $this->merchant) {
            $this->merchant = new Merchant();
        }

        return $this->merchant;
    }

    public function createPaymentMethod()
    {
        if (! $this->paymentMethod) {
            $this->paymentMethod = new PaymentMethod();
        }

        return $this->paymentMethod;
    }

    public function createSplit()
    {
        if (! $this->split) {
            $this->split = new Split();
        }

        return $this->split;
    }

    public function addSplit(Structures\Merchant $merchant, $amount)
    {
        $this->splitMerchant    = $merchant;
        $this->splitAmount      = $amount;

        return $this;
    }
}
