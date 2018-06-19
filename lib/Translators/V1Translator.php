<?php

namespace StackPay\Payments\Translators;

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures;

class V1Translator
{
    public function buildAccountElement(Structures\Account $account)
    {
        $accountElement = [];

        if ($account->isBankAccount()) {
            $accountElement = [
                'Number'        => $account->number,
                'RoutingNumber' => $account->routingNumber,
            ];
        } elseif ($account->isCardAccount()) {
            $accountElement = [
                'Number'        => $account->number,
                'ExpireDate'    => $account->expireDate,
                'Cvv2'          => $account->cvv2,
            ];
        } else {
            throw (new Exceptions\InvalidAccountTypeException())->setAccountType($account->type);
        }

        return $accountElement;
    }

    public function buildAccountHolderElement(Structures\AccountHolder $accountHolder)
    {
        $accountHolderElement = [
            'Name'              => $accountHolder->name,
            'BillingAddress'    => $this->buildAddressElement($accountHolder->billingAddress),
        ];

        return $accountHolderElement;
    }

    public function buildAddressElement(Structures\Address $address)
    {
        $addressElement = [
            'Address1'  => $address->address1,
            'Address2'  => $address->address2,
            'City'      => $address->city,
            'State'     => $address->state,
            'Zip'       => $address->postalCode,
            'Country'   => $address->country,
        ];

        return $addressElement;
    }

    public function buildMerchantApplicationElement(Structures\MerchantApplication $merchantApplication)
    {
        $merchantApplicationElement = [
            'ExternalId'        => $merchantApplication->externalId,
            'RateName'          => $merchantApplication->rate,
            'ApplicationName'   => $merchantApplication->name,
        ];

        return $merchantApplicationElement;
    }

    public function buildTokenElement(Structures\Token $token)
    {
        $tokenElement = [
            'Token' => $token->token
        ];

        return $tokenElement;
    }
}
