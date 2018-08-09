<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

use StackPay\Payments\AccountTypes;

trait ScheduledTransaction
{
    public function requestScheduledTransaction($transaction)
    {
        $body = [
            'external_id'   => $transaction->object()->externalId(),
            'merchant_id'   => $transaction->object()->merchant()->id(),
            'scheduled_at'  => $transaction->object()->scheduledAt()->format('Y-m-d'),
            'timezone'      => $transaction->object()->scheduledAt()->getTimezone()->getName(),
            'currency_code' => $transaction->object()->currencyCode(),
            'amount'        => $transaction->object()->amount()
        ];

        if ($transaction->object()->paymentMethod() &&
            $transaction->object()->paymentMethod()->id()
        ) {
            $body['payment_method']['method']   = 'id';
            $body['payment_method']['id']       = $transaction->object()->paymentMethod()->id();
        }

        if ($transaction->object()->account()) {
            $body['payment_method']['account_number']   = $transaction->object()->account()->number();
            $body['payment_method']['billing_name']     = $transaction->object()->accountHolder()->name();

            if ($transaction->object()->account()->type() == AccountTypes::CHECKING ||
                $transaction->object()->account()->type() == AccountTypes::SAVINGS
            ) {
                $body['payment_method']['method']           = 'bank_account';
                $body['payment_method']['type']             = $transaction->object()->account()->type();
                $body['payment_method']['routing_number']   = $transaction->object()->account()->routingNumber();
            }

            if ($transaction->object()->account()->type() == AccountTypes::AMEX ||
                $transaction->object()->account()->type() == AccountTypes::DISCOVER ||
                $transaction->object()->account()->type() == AccountTypes::MASTERCARD ||
                $transaction->object()->account()->type() == AccountTypes::VISA
            ) {
                $body['payment_method']['method']       = 'credit_card';
                $body['payment_method']['type']         = $transaction->object()->account()->type();
                $body['payment_method']['cvv2']         = $transaction->object()->account()->cvv2();

                if ($transaction->object()->account()->expireMonth() &&
                    $transaction->object()->account()->expireYear()
                ) {
                    $body['payment_method']['expiration_month'] = $transaction->object()->account()->expireMonth();
                    $body['payment_method']['expiration_year']  = $transaction->object()->account()->expireYear();
                } elseif ($transaction->object()->account()->expireDate()) {
                    $body['payment_method']['expiration_month'] = substr($transaction->object()->account()->expireDate(), 0, 2);
                    $body['payment_method']['expiration_year']  = substr($transaction->object()->account()->expireDate(), 2, 2);
                }
            }

            $body['payment_method']['billing_address_1'] = $transaction->object()->accountHolder()->billingAddress()->address1();

            if ($transaction->object()->accountHolder()->billingAddress()->address2()) {
                $body['payment_method']['billing_address_2'] = $transaction->object()->accountHolder()->billingAddress()->address2();
            }
            $body['payment_method']['billing_city']     = $transaction->object()->accountHolder()->billingAddress()->city();
            $body['payment_method']['billing_state']    = $transaction->object()->accountHolder()->billingAddress()->state();
            $body['payment_method']['billing_zip']      = $transaction->object()->accountHolder()->billingAddress()->postalCode();
        }

        if ($transaction->object()->token()) {
            $body['payment_method']['method']   = 'token';
            $body['payment_method']['token']    = $transaction->object()->token()->token();
        }

        if ($transaction->object()->split()) {
            $body['split_merchant_id']  = $transaction->object()->split()->merchant()->id();
            $body['split_amount']       = $transaction->object()->split()->amount();
        }

        $transaction->request()->body($body);
    }
}
