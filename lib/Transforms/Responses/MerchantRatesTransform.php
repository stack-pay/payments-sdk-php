<?php

namespace StackPay\Payments\Transforms\Responses;

trait MerchantRatesTransform
{
    public function responseMerchantRates($transaction)
    {
        foreach ($transaction->response()->body()['Rates'] as $key => $value) {
            $transaction->object()->appendRate()
                ->setBankFeeRate($value['bank_account']['fee_rate'])
                ->setBankFeeTransaction($value['bank_account']['fee_transaction'])
                ->setBankFeeNotes($value['bank_account']['fee_notes'])
                ->setCardFeeRate($value['credit_card']['fee_rate'])
                ->setCardFeeTransaction($value['credit_card']['fee_transaction'])
                ->setCardFeeNotes($value['credit_card']['fee_notes'])
                ->setName($value['rate_name']);
        }
    }
}
