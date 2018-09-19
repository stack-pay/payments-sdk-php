<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait AuthTransform
{
    public function requestAuth($transaction)
    {
        $body = [];
        $auth = $transaction->object();

        $this->requestAuth_fillBase( $body, $auth );
        $this->requestAuth_fillToken( $body, $auth );
        $this->requestAuth_fillMasterPass( $body, $auth );
        $this->requestAuth_fillPaymentMethod( $body, $auth );
        $this->requestAuth_fillAccount( $body, $auth );
        $this->requestAuth_fillSplit( $body, $auth );
        $this->requestAuth_fillCustomer( $body, $auth );

        $transaction->request()->body($body);
    }

    public function requestAuth_fillBase(
        &$body,
        $auth
    ) {
        $body[ 'Merchant' ] = $auth->merchant()->id();
        $body[ 'Order']    = [
            'Transaction'   => [
                'Type'          => 'Auth',
                'Currency'      => $auth->currency(),
                'Amount'        => $auth->amount(),
                'InvoiceNumber' => $auth->invoiceNumber() ?: null,
                'ExternalId'    => $auth->externalID() ?: null,
                'Comment1'      => $auth->comment1() ?: null,
                'Comment2'      => $auth->comment2() ?: null,
            ]
        ];
    }

    public function requestAuth_fillToken(
        &$body,
        $auth
    ) {
        if ($auth->token()) {
            $body['Order']['Token'] = $auth->token()->token();
        }
    }

    public function requestAuth_fillMasterPass(
        &$body,
        $auth
    ) {
        if ($auth->masterPassTransactionId()) {
            $body['Order']['MasterPass']['TransactionId'] = $auth->masterPassTransactionId();
        }
    }

    public function requestAuth_fillPaymentMethod(
        &$body,
        $auth
    ) {
        if ($auth->paymentMethod() &&
            $auth->paymentMethod()->id()
        ) {
            $body['Order']['PaymentMethod'] = $auth->paymentMethod()->id();
        }
    }

    public function requestAuth_fillAccount(
        &$body,
        $auth
    ) {
        if ($auth->account()) {
            $body['Order']['SavePaymentMethod'] = $auth->account()->savePaymentMethod();
            $body['Order']['Account']           = $this->requestAccount($auth->account());
            $body['Order']['AccountHolder']     = $this->requestAccountHolder($auth->accountHolder());
        }
    }

    public function requestAuth_fillSplit(
        &$body,
        $auth
    ) {
        if ($auth->split()) {
            $body['Order']['Transaction']['SplitAmount']   = $auth->split()->amount();
            $body['Order']['Transaction']['SplitMerchant'] = $auth->split()->merchant()->id();
        }
    }

    public function requestAuth_fillCustomer(
        &$body,
        $auth
    ) {
        if ($auth->customer() &&
            $auth->customer()->id()
        ) {
            $body['Order']['Customer'] = $auth->customer()->id();
        }
    }
}
