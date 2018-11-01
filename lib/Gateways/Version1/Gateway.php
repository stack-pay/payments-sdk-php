<?php

namespace StackPay\Payments\Gateways\Version1;

use StackPay\Payments\Gateways;
use StackPay\Payments\Structures;
use StackPay\Payments\Transforms;
use StackPay\Payments\URLs;

class Gateway extends Gateways\Gateway
{
    use Transforms\Requests\Transactions\HeaderTransform;
    use Transforms\Requests\Transactions\HashTransform;
    use Transforms\Requests\Transactions\AuthTransform;
    use Transforms\Requests\Transactions\V1Transform;
    use Transforms\Requests\Transactions\JSONTransform;
    use Transforms\Requests\Transactions\IdempotencyTransform;

    use Transforms\Requests\Structures\AccountTransform;
    use Transforms\Requests\Structures\AccountHolderTransform;
    use Transforms\Requests\Structures\BillingAddressTransform;
    use Transforms\Requests\Structures\CreatePaymentMethodTransform;
    use Transforms\Requests\Structures\AuthTransform;
    use Transforms\Requests\Structures\CaptureTransform;
    use Transforms\Requests\Structures\SaleTransform;
    use Transforms\Requests\Structures\VoidTransform;
    use Transforms\Requests\Structures\RefundTransform;
    use Transforms\Requests\Structures\CreditTransform;
    use Transforms\Requests\Structures\MerchantLimitsTransform;
    use Transforms\Requests\Structures\MerchantLinkTransform;
    use Transforms\Requests\Structures\ScheduledTransaction;

    use Transforms\Responses\JSONTransform;
    use Transforms\Responses\ErrorTransform;
    use Transforms\Responses\V1Transform;
    use Transforms\Responses\HashTransform;
    use Transforms\Responses\TokenTransform;
    use Transforms\Responses\CreatePaymentMethodTransform;
    use Transforms\Responses\AuthTransform;
    use Transforms\Responses\CaptureTransform;
    use Transforms\Responses\VoidTransform;
    use Transforms\Responses\RefundTransform;
    use Transforms\Responses\CreditTransform;
    use Transforms\Responses\MerchantRatesTransform;
    use Transforms\Responses\MerchantLimitsTransform;
    use Transforms\Responses\MerchantLinkTransform;
    use Transforms\Responses\ScheduledTransaction;

    protected $application              = 'PaymentSystem';
    protected $apiVersion               = 'v1';
    protected $createTokenURL           = 'api/token';
    protected $createPaymentMethodsURL  = 'api/paymethods';
    protected $paymentsURL              = 'api/payments';
    protected $merchantRatesURL         = 'api/merchants/rates';
    protected $merchantLimitsURL        = 'api/merchants/limits';
    protected $merchantLinkURL          = 'api/merchants/link';
    protected $scheduledTransactionURL  = 'api/scheduled-transactions';

    protected function execute(&$transaction, $method = 'post')
    {
        $transaction->request()->rawBody(
            json_encode($transaction->request()->body(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $this->requestHeaders($transaction->request());
        $this->requestHash($transaction->request());
        $this->requestIdempotency($transaction);
        $this->requestV1($transaction->request());
        $this->requestClientAuth($transaction->request());
        $this->requestJSON($transaction->request());

        $this->sendRequest($transaction, $method);

        $this->responseJSON($transaction->response());
        $this->responseError($transaction->response());

        if (! empty($transaction->response()->body())) {
            // Transform the response
            $this->responseV1($transaction->response());

            $transaction->response()->rawBody(json_encode($transaction->response()->body()));

            $this->responseHash($transaction->response());

            $transaction->response()->lock();
        }
    }

    public function createToken($transaction)
    {
        $this->requestCreatePaymentMethod($transaction);

        $transaction->request()->endpoint($this->createTokenURL);
        $transaction->request()->rawBody(
            json_encode($transaction->request()->body(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $this->requestPublicAuth($transaction->request());
        $this->requestJSON($transaction->request());

        $this->sendRequest($transaction);

        $this->responseJSON($transaction->response());
        $this->responseError($transaction->response());
        $this->responseV1($transaction->response());

        $transaction->response()->rawBody(json_encode($transaction->response()->body()));
        $transaction->response()->lock();

        $this->responseToken($transaction);

        return $transaction->object();
    }

    public function createPaymentMethod($transaction)
    {
        $transaction->request()->endpoint($this->createPaymentMethodsURL);
        $transaction->request()->hashKey($this->privateKey);

        $transaction->response()->hashKey($this->privateKey);

        $this->requestCreatePaymentMethod($transaction);

        $this->execute($transaction);

        $this->responseCreatePaymentMethod($transaction);

        return $transaction->object();
    }

    private function executePayment($transaction)
    {
        $transaction->request()->endpoint($this->paymentsURL);
        $transaction->request()->hashKey($transaction->object()->merchant()->hashKey());

        $transaction->response()->hashKey($transaction->object()->merchant()->hashKey());

        $this->execute($transaction);
    }

    public function auth($transaction)
    {
        $this->requestAuth($transaction);

        $this->executePayment($transaction);

        $this->responseAuth($transaction);

        return $transaction->object();
    }

    public function capture($transaction)
    {
        $this->requestCapture($transaction);

        $this->executePayment($transaction);

        $this->responseCapture($transaction);

        return $transaction->object();
    }

    public function refund($transaction)
    {

        $this->requestRefund($transaction);

        $this->executePayment($transaction);

        $this->responseRefundOrVoid($transaction);

        return $transaction->object();
    }

    public function sale($transaction)
    {
        $this->requestSale($transaction);

        $this->executePayment($transaction);

        $this->responseAuth($transaction);

        $this->responseCapture($transaction);

        return $transaction->object();
    }

    public function voidTransaction($transaction)
    {
        $this->requestVoid($transaction);

        $this->executePayment($transaction);

        $this->responseRefundOrVoid($transaction);

        return $transaction->object();
    }

    public function credit($transaction)
    {
        $this->requestCredit($transaction);

        $this->executePayment($transaction);

        $this->responseCredit($transaction);

        return $transaction->object();
    }

    public function merchantRates($transaction)
    {
        $transaction->request()->endpoint($this->merchantRatesURL);
        $transaction->request()->hashKey($this->privateKey);

        $transaction->response()->hashKey($this->privateKey);

        $this->execute($transaction);

        $this->responseMerchantRates($transaction);

        return $transaction->object();
    }

    public function responseRefundOrVoid($transaction)
    {
        if (array_key_exists('Refund', $transaction->response()->body())) {
            $this->responseRefund($transaction);
        }

        if (array_key_exists('Void', $transaction->response()->body())) {
            $this->responseVoid($transaction);
        }
    }

    public function merchantLimits($transaction)
    {
        $transaction->request()->endpoint($this->merchantLimitsURL);
        $transaction->request()->hashKey($transaction->object()->hashKey());

        $transaction->response()->hashKey($transaction->object()->hashKey());

        $this->requestMerchantLimits($transaction);

        $this->execute($transaction);

        $this->responseMerchantLimits($transaction);

        return $transaction->object();
    }

    public function generateMerchantLink($transaction)
    {
        $transaction->request()->endpoint($this->merchantLinkURL);
        $transaction->request()->hashKey($this->privateKey);

        $transaction->response()->hashKey($this->privateKey);

        $this->requestMerchantLink($transaction);

        $this->execute($transaction);

        $this->responseMerchantLink($transaction);

        return $transaction->object();
    }

    public function createScheduledTransaction($transaction)
    {
        $transaction->request()->endpoint($this->scheduledTransactionURL);
        $transaction->request()->hashKey($transaction->object()->merchant()->hashKey());

        $transaction->response()->hashKey($transaction->object()->merchant()->hashKey());

        $this->requestScheduledTransaction($transaction);

        $this->execute($transaction);

        $this->responseScheduledTransaction($transaction);

        return $transaction->object();
    }

    public function getScheduledTransaction($transaction)
    {
        $transaction->request()->endpoint($this->scheduledTransactionURL . '/' . $transaction->object()->id());
        $transaction->request()->hashKey($this->privateKey);

        $transaction->response()->hashKey($this->privateKey);

        $this->execute($transaction, 'GET');

        $this->responseGetScheduledTransaction($transaction);

        return $transaction->object();
    }

    public function deleteScheduledTransaction($transaction)
    {
        $transaction->request()->endpoint($this->scheduledTransactionURL . '/' . $transaction->object()->id());
        $transaction->request()->hashKey($this->privateKey);

        $transaction->response()->hashKey($this->privateKey);

        $this->execute($transaction, 'DELETE');

        return $transaction->object();
    }
}
