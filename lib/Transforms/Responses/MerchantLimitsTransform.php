<?php

namespace StackPay\Payments\Transforms\Responses;

trait MerchantLimitsTransform
{
    public function responseMerchantLimits($transaction)
    {
        $transaction->object()->setCreditCardTransactionLimit(
            $transaction->response()->body()['Limits']['credit_card_transaction_limit']
        );

        $transaction->object()->setCreditCardMonthlyLimit(
            $transaction->response()->body()['Limits']['credit_card_monthly_limit']
        );

        $transaction->object()->setCreditCardCurrentVolume(
            $transaction->response()->body()['Limits']['credit_card_current_volume']
        );

        $transaction->object()->setACHTransactionLimit(
            $transaction->response()->body()['Limits']['ach_transaction_limit']
        );

        $transaction->object()->setACHMonthlyLimit(
            $transaction->response()->body()['Limits']['ach_monthly_limit']
        );

        $transaction->object()->setACHCurrentVolume(
            $transaction->response()->body()['Limits']['ach_current_volume']
        );
    }
}
