<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Auth extends Transaction implements Interfaces\Auth
{
    public $type = 'Auth';
    public $account;
    public $accountHolder;
    public $token;
    public $masterPassTransactionId;
    public $softDescriptor;

    public function type()
    {
        return 'Auth';
    }

    public function account()
    {
        return $this->account;
    }

    public function accountHolder()
    {
        return $this->accountHolder;
    }

    public function token()
    {
        return $this->token;
    }

    public function masterPassTransactionId()
    {
        return $this->masterPassTransactionId;
    }

    // ----

    public function setAccount(Interfaces\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    public function setAccountHolder(Interfaces\AccountHolder $accountHolder = null)
    {
        $this->accountHolder = $accountHolder;

        return $this;
    }

    public function setToken(Interfaces\Token $token = null)
    {
        $this->token = $token;

        return $this;
    }

    public function setMasterPassTransactionId($masterPassTransactionId = null)
    {
        $this->masterPassTransactionId = $masterPassTransactionId;

        return $this;
    }

    // ---------

    public function createToken()
    {
        if (! $this->token) {
            $this->token = new Token();
        }

        return $this->token;
    }

    public function createAccount()
    {
        if (! $this->account) {
            $this->account = new Account();
        }

        return $this->account;
    }
}
