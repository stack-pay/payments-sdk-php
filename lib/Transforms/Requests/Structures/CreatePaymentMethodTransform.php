<?php

namespace StackPay\Payments\Transforms\Requests\Structures;

trait CreatePaymentMethodTransform
{
    public function requestCreatePaymentMethod($transaction)
    {
        $body = [ 'Order' => [] ];

        if (method_exists($transaction->object(),'token') &&
            $transaction->object()->token()
        ) {
            $body['Order']['Token'] = $transaction->object()->token();
        } else {
            $body['Order']['Account']       = $this->requestAccount($transaction->object()->account());
            $body['Order']['AccountHolder'] = $this->requestAccountHolder($transaction->object()->accountHolder());
        }

        if ($transaction->object()->customer() &&
            $transaction->object()->customer()->id()
        ) {
            $body['Order']['Customer'] = $transaction->object()->customer()->id();
        }

        $transaction->request()->body($body);
    }
}
