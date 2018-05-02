<?php

namespace StackPay\Payments\Transforms\Responses;

trait MerchantRatesTransform
{
    public function responseMerchantRates($transaction)
    {
        foreach ($transaction->response()->body()['Rates'] as $key => $value) {
            $transaction->object()->appendRate()
                ->setFeeRate($value['fee_rate'])
                ->setFeeTransaction($value['fee_transaction'])
                ->setFeeNotes($value['fee_notes'])
                ->setName($key);
        }
    }
}
