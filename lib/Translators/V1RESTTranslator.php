<?php

namespace StackPay\Payments\Translators;

use StackPay\Payments\Exceptions;
use StackPay\Payments\Structures;

class V1RESTTranslator
{
    public function buildAccountHolderElement(Structures\PaymentMethod $paymentMethod)
    {
        $accountHolderElement = [
            'billing_name'      => $paymentMethod->billingName,
            'billing_address_1' => $paymentMethod->billingAddress1,
            'billing_city'      => $paymentMethod->billingCity,
            'billing_state'     => $paymentMethod->billingState,
            'billing_zip'       => $paymentMethod->billingZip,
            'billing_country'   => $paymentMethod->billingCountry,
        ];

        if ($paymentMethod->billingAddress2) {
            $accountHolderElement['billing_address_2'] = $paymentMethod->billingAddress2;
        }

        return $accountHolderElement;
    }

    public function buildCustomerElement(Structures\Customer $customer)
    {
        $customerElement = [
            'first_name'    => $customer->firstName,
            'last_name'     => $customer->lastName,
        ];

        return $customerElement;
    }

    public function buildPaymentMethodElement(Structures\PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->id) {
            $paymentMethodElement = $this->buildPaymentMethodElementAsId($paymentMethod);
        } elseif ($paymentMethod->token) {
            $paymentMethodElement = $this->buildPaymentMethodElementAsToken($paymentMethod);
        } elseif ($paymentMethod->isBankAccount()) {
            $paymentMethodElement = $this->buildPaymentMethodElementAsBankAccount($paymentMethod);
        } elseif ($paymentMethod->isCardAccount()) {
            $paymentMethodElement = $this->buildPaymentMethodElementAsCardAccount($paymentMethod);
        } else {
            throw (new Exceptions\InvalidAccountTypeException())->setAccountType($paymentMethod->type);
        }

        return $paymentMethodElement;
    }

    public function buildPaymentMethodElementAsId(Structures\PaymentMethod $paymentMethod)
    {
        $paymentMethodElement = [
            'method'    => 'id',
            'id'        => $paymentMethod->id,
        ];

        return $paymentMethodElement;
    }

    public function buildPaymentMethodElementAsToken(Structures\PaymentMethod $paymentMethod)
    {
        $paymentMethodElement = [
            'method'    => 'token',
            'token'     => $paymentMethod->token,
        ];

        return $paymentMethodElement;
    }

    public function buildPaymentMethodElementAsBankAccount(Structures\PaymentMethod $paymentMethod)
    {
        $paymentMethodElement = [
            'method'            => 'bank_account',
            'type'              => $paymentMethod->type,
            'account_number'    => $paymentMethod->accountNumber,
            'routing_number'    => $paymentMethod->routingNumber,
        ];

        $paymentMethodElement = array_merge(
            $paymentMethodElement,
            $this->buildAccountHolderElement($paymentMethod)
        );

        if ($paymentMethod->customer) {
            $paymentMethodElement['customer_id'] = $paymentMethod->customer->id;
        }

        return $paymentMethodElement;
    }

    public function buildPaymentMethodElementAsCardAccount(Structures\PaymentMethod $paymentMethod)
    {
        $paymentMethodElement = [
            'method'            => 'credit_card',
            'type'              => $paymentMethod->type,
            'account_number'    => $paymentMethod->accountNumber,
            'cvv2'              => $paymentMethod->cvv2,
            'expiration_month'  => $paymentMethod->expirationMonth,
            'expiration_year'   => $paymentMethod->expirationYear,
        ];

        $paymentMethodElement = array_merge(
            $paymentMethodElement,
            $this->buildAccountHolderElement($paymentMethod)
        );

        if ($paymentMethod->customer) {
            $paymentMethodElement['customer_id'] = $paymentMethod->customer->id;
        }

        return $paymentMethodElement;
    }

    public function buildScheduledTransactionElement(Structures\ScheduledTransaction $scheduledTransaction)
    {
        $scheduledTransactionElement = [
            'external_id'       => $scheduledTransaction->externalId,
            'merchant_id'       => $scheduledTransaction->merchant->id,
            'scheduled_at'      => $scheduledTransaction->scheduledAt->format('Y-m-d'),
            'currency_code'     => $scheduledTransaction->currencyCode,
            'amount'            => $scheduledTransaction->amount,
            'payment_method'    => $this->buildPaymentMethodElement($scheduledTransaction->paymentMethod),
        ];

        if ($scheduledTransaction->splitMerchant) {
            $scheduledTransactionElement['split_merchant_id']   = $scheduledTransaction->splitMerchant->id;
            $scheduledTransactionElement['split_amount']        = $scheduledTransaction->splitAmount;
        }

        return $scheduledTransactionElement;
    }

    public function buildPaymentPlanCopyElement(Structures\PaymentPlan $paymentPlan)
    {
        $paymentPlanElement = [
            'payment_plan_id'   => $paymentPlan->id,
        ];

        if ($paymentPlan->splitMerchant) {
            $paymentPlanElement['split_merchant_id'] = $paymentPlan->splitMerchant->id;
        }
        if ($paymentPlan->paymentPriority) {
            $paymentPlanElement['payment_priority'] = $paymentPlan->paymentPriority;
        }

        return $paymentPlanElement;
    }

    public function buildPaymentPlanCreateSubscriptionElement(Structures\Subscription $object)
    {
        $element = [
            'external_id'         => (string) $object->externalId,
            'currency_code'       => $object->currencyCode,
            'amount'              => $object->amount,
            'down_payment_amount' => $object->amount,
            'payment_method'      => $this->buildPaymentMethodElement($object->paymentMethod),
        ];

        if ($object->day) {
            $element['day'] = $object->day;
        }

        if ($object->splitAmount) {
            $element['split_amount'] = $object->splitAmount;
        }

        return $element;
    }
}
