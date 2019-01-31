<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait PaymentPlanTransform
{
    public function requestCopyPaymentPlan($transaction)
    {
        $request = [
            'payment_plan_id'   => $transaction->object()->id(),
            'merchant_id'       => $transaction->object()->merchant()->id(),
        ];
        if (!is_null($transaction->object()->splitMerchant())) {
            $request['split_merchant_id'] = $transaction->object()->splitMerchant()->id();
        }
        if (!is_null($transaction->object()->paymentPriority())) {
            $request['payment_priority'] = $transaction->object()->paymentPriority();
        }
        $transaction->request()->body($request);
    }

    public function requestEditPaymentPlan($transaction)
    {
        $request = [];
        if (!is_null($transaction->object()->name())) {
            $request['name'] = $transaction->object()->name();
        }
        if (!is_null($transaction->object()->downPaymentAmount())) {
            $request['down_payment_amount'] = $transaction->object()->downPaymentAmount();
        }
        if (!is_null($transaction->object()->merchant())) {
            if (!is_null($transaction->object()->merchant()->id())) {
                $request['merchant_id'] = $transaction->object()->merchant()->id();
            }
        }
        if (!is_null($transaction->object()->configuration())) {
            $request['configuration'] = [];
            if (!is_null($transaction->object()->configuration()->months())) {
                $request['configuration']['months'] = $transaction->object()->configuration()->months();
            }
            if (!is_null($transaction->object()->configuration()->day())) {
                $request['configuration']['day'] = $transaction->object()->configuration()->day();
            }
        }
        if (!is_null($transaction->object()->isActive())) {
            $request['is_active'] = $transaction->object()->isActive();
        }
        if (!is_null($transaction->object()->splitMerchant())) {
            $request['split_merchant_id'] = $transaction->object()->splitMerchant()->id();
        }
        if (!is_null($transaction->object()->paymentPriority())) {
            $request['payment_priority'] = $transaction->object()->paymentPriority();
        }
        $transaction->request()->body($request);
    }

    public function requestCreatePaymentPlanSubscription($transaction)
    {
        $body = [
            'external_id'           => (string)$transaction->object()->externalID(),
            'amount'                => $transaction->object()->amount(),
            'down_payment_amount'   => $transaction->object()->downPaymentAmount(),
            'currency_code'         => $transaction->object()->currencyCode(),
            'payment_method'        => [
                'method'            => 'id',
                'id'                => $transaction->object()->paymentMethod()->id(),
            ],
        ];

        if ($transaction->object()->splitAmount()) {
            $body['split_amount'] = $transaction->object()->splitAmount();
        }

        if ($transaction->object()->splitMerchant()) {
            $body['split_merchant_id'] = $transaction->object()->splitMerchant()->id();
        }

        if ($transaction->object()->day()) {
            $body['day'] = $transaction->object()->day();
        }

        $transaction->request()->body($body);
    }

    public function requestEditPaymentPlanSubscription($transaction)
    {
        $body = [
            'payment_method'        => [
                'type'              => $transaction->object()->paymentMethod()->account()->type(),
                'account_last_four' => $transaction->object()->paymentMethod()->account()->last4(),
                'expiration_month'  => $transaction->object()->paymentMethod()->account()->expireMonth(),
                'expiration_year'   => $transaction->object()->paymentMethod()->account()->expireYear(),
                'routing_last_four' => $transaction->object()->paymentMethod()->account()->routingLast4(),
                'billing_name'      => $transaction->object()->paymentMethod()->accountHolder()->name(),
                'billing_address_1' => $transaction->object()->paymentMethod()->accountHolder()->billingAddress()->address1(),
                'billing_address_2' => $transaction->object()->paymentMethod()->accountHolder()->billingAddress()->address2(),
                'billing_city'      => $transaction->object()->paymentMethod()->accountHolder()->billingAddress()->city(),
                'billing_state'     => $transaction->object()->paymentMethod()->accountHolder()->billingAddress()->state(),
                'billing_zip'       => $transaction->object()->paymentMethod()->accountHolder()->billingAddress()->postalCode(),
                'billing_country'   => $transaction->object()->paymentMethod()->accountHolder()->billingAddress()->country(),
                'customer_id'       => $transaction->object()->paymentMethod()->customer()->id(),
            ],
        ];

        $transaction->request()->body($body);
    }
}
