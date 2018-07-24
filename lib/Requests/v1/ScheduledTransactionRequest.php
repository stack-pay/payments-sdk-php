<?php

namespace StackPay\Payments\Requests\v1;

use StackPay\Payments\StackPay;
use StackPay\Payments\Structures;

class ScheduledTransactionRequest extends Request
{
    public static function create(Structures\ScheduledTransaction $scheduledTransaction)
    {
        $request = new self();

        $request->method    = 'POST';
        $request->endpoint  = '/api/scheduled-transactions';
        $request->hashKey   = $scheduledTransaction->merchant->hashKey;
        $request->body      = $this->restTranslator->buildScheduledTransactionElement($scheduledTransaction);

        return $request;
    }

    public static function delete(Structures\ScheduledTransaction $scheduledTransaction)
    {
        $request = new self();

        $request->method    = 'DELETE';
        $request->endpoint  = '/api/scheduled-transactions/'. $scheduledTransaction->id;
        $request->hashKey   = StackPay::$privateKey;
        $request->body      = null;

        return $request;
    }

    public static function get(Structures\ScheduledTransaction $scheduledTransaction)
    {
        $request = new self();

        $request->method    = 'GET';
        $request->endpoint  = '/api/scheduled-transactions/'. $scheduledTransaction->id;
        $request->hashKey   = StackPay::$privateKey;
        $request->body      = null;

        return $request;
    }

    public static function retry(Structures\ScheduledTransaction $scheduledTransaction)
    {
        $request = new self();

        $request->method    = 'POST';
        $request->endpoint  = '/api/scheduled-transactions/'. $scheduledTransaction->id .'/attempts';
        $request->hashKey   = StackPay::$privateKey;
        $request->body      = null;

        if ($scheduledTransaction->paymentMethod) {
            $request->body  = [
                'payment_method' => $this->restTranslator->buildPaymentMethodElement(
                    $scheduledTransaction->paymentMethod
                )
            ];
        }

        return $request;
    }
}
