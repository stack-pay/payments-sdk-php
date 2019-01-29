<?php

namespace StackPay\Payments;

use Curl\Curl;
use GuzzleHttp\Client;

use StackPay\Payments\Structures\Transactions;

/**
 * The core SDK class.
 */
class StackPay
{
    public static $publicKey;
    public static $privateKey;
    public static $mode = 'production';
    public static $currency = 'USD';
    public static $httpClient;
    public static $baseUrl;

    public static $gateway;

    public function __construct($publicKey, $privateKey, $httpClient = null)
    {
        self::$publicKey    = $publicKey;
        self::$privateKey   = $privateKey;
        self::$httpClient   = $httpClient ?: new Client();

        self::$gateway = new Gateways\Version1\Gateway($publicKey, $privateKey);
    }

    public function enableTestMode($alternateUrl = null)
    {
        self::$mode = 'development';

        self::$baseUrl = $alternateUrl;

        self::$gateway->enableTestMode();
        self::$gateway->baseURL($alternateUrl);

        return $this;
    }

    public function setCurlProvider(Providers\CurlProviderInterface $curlProvider)
    {
        self::$gateway->curlProvider($curlProvider);

        return $this;
    }

    public function setCurrency($currency)
    {
        self::$currency = $currency;

        self::$gateway->currency($currency);

        return $this;
    }

    public function setHttpClient(Client $httpClient)
    {
        self::$httpClient = $httpClient;

        return $this;
    }

    public function createToken(
        Interfaces\Token $token,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($token);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->createToken($transaction);
    }

    public function createTokenWithAccountDetails(
        Interfaces\Account $account,
        Interfaces\AccountHolder $accountHolder,
        Interfaces\Customer $customer = null,
        $idempotencyKey = null
    ) {
        return $this->createToken(
            (new Structures\Token())
                ->setAccount($account)
                ->setAccountHolder($accountHolder)
                ->setCustomer($customer),
            $idempotencyKey
        );
    }

    public function createPaymentMethod(
        Interfaces\PaymentMethod $paymentMethod,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($paymentMethod);
        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->createPaymentMethod($transaction);
    }

    public function createPaymentMethodWithAccountDetails(
        Interfaces\Account $account,
        Interfaces\AccountHolder $accountHolder,
        Interfaces\Customer $customer = null,
        $idempotencyKey = null
    ) {
        return $this->createPaymentMethod(
            (new Structures\PaymentMethod())
                ->setAccount($account)
                ->setAccountHolder($accountHolder)
                ->setCustomer($customer),
            $idempotencyKey
        );
    }

    public function createPaymentMethodWithToken(
        Interfaces\Token $token,
        $idempotencyKey = null
    ) {
        return $this->createPaymentMethod(
            $token,
            $idempotencyKey
        );
    }

    public function processTransaction(
        Interfaces\Transaction $transaction,
        $idempotencyKey = null
    ) {
        if (method_exists($transaction, 'currency') &&
            method_exists($transaction, 'setCurrency') &&
            ! $transaction->currency()
        ) {
            $transaction->setCurrency(self::$gateway->currency());
        }

        switch ($transaction->type()) {
            case 'Auth':
                return $this->auth($transaction, $idempotencyKey);

            case 'Capture':
                return $this->capture($transaction, $idempotencyKey);

            case 'Refund':
                return $this->refund($transaction, $idempotencyKey);

            case 'Sale':
                return $this->sale($transaction, $idempotencyKey);

            case 'Void':
                return $this->voidTransaction($transaction, $idempotencyKey);

            default:
                throw new \Exception('Unknown Payment type');
        }
    }

    public function auth(
        Interfaces\Auth $auth,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($auth);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->auth($transaction);
    }

    public function authWithPaymentMethod(
        Interfaces\PaymentMethod $paymentMethod,
        Interfaces\Merchant $merchant,
        $amount,
        Interfaces\Split $split = null,
        $idempotencyKey = null,
        $currency = null
    ) {
        return $this->auth(
            (new Structures\Auth())
               ->setPaymentMethod($paymentMethod)
               ->setMerchant($merchant)
               ->setAmount($amount)
               ->setCurrency($currency ?: self::$gateway->currency())
               ->setSplit($split),
            $idempotencyKey
        );
    }

    public function authWithMasterPass(
        $masterPassTransactionId,
        Interfaces\Merchant $merchant,
        $amount,
        Interfaces\Customer $customer = null,
        Interfaces\Split $split = null,
        $idempotencyKey = null,
        $currency = null
    ) {
        return $this->auth(
            (new Structures\Auth())
               ->setMasterPassTransactionId($masterPassTransactionId)
               ->setMerchant($merchant)
               ->setAmount($amount)
               ->setCustomer($customer)
               ->setCurrency($currency ?: self::$gateway->currency())
               ->setSplit($split),
            $idempotencyKey
        );
    }

    public function authWithAccountDetails(
        Interfaces\Account $account,
        Interfaces\AccountHolder $accountHolder,
        Interfaces\Merchant $merchant,
        $amount,
        Interfaces\Customer $customer = null,
        Interfaces\Split $split = null,
        $idempotencyKey = null,
        $currency = null
    ) {
        return $this->auth(
            (new Structures\Auth())
                ->setAccount($account)
                ->setAccountHolder($accountHolder)
                ->setMerchant($merchant)
                ->setAmount($amount)
                ->setCustomer($customer)
                ->setCurrency($currency ?: self::$gateway->currency())
                ->setSplit($split),
            $idempotencyKey
        );
    }

    public function authWithToken(
        Interfaces\Token $token,
        Interfaces\Merchant $merchant,
        $amount,
        Interfaces\Customer $customer = null,
        Interfaces\Split $split = null,
        $idempotencyKey = null,
        $currency = null
    ) {
        return $this->auth(
            (new Structures\Auth())
                ->setToken($token)
                ->setMerchant($merchant)
                ->setAmount($amount)
                ->setCustomer($customer)
                ->setCurrency($currency ?: self::$gateway->currency())
                ->setSplit($split),
            $idempotencyKey
        );
    }

    public function capture(
        Interfaces\Capture $capture,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($capture);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->capture($transaction);
    }

    public function captureWithOriginalTransaction(
        Interfaces\Transaction $originalTransaction,
        $amount,
        $splitAmount,
        Interfaces\Merchant $merchant = null,
        $idempotencyKey = null
    ) {
        $capture = (new Structures\Capture())
            ->setOriginalTransaction($originalTransaction)
            ->setMerchant($merchant ? $merchant : $originalTransaction->merchant())
            ->setAmount($amount);

        $capture->createSplit()->setAmount($splitAmount);

        return $this->capture($capture, $idempotencyKey);
    }

    public function refund(
        Interfaces\Refund $refund,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($refund);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->refund($transaction);
    }

    public function refundWithOriginalTransaction(
        Interfaces\Transaction $originalTransaction,
        $amount,
        Interfaces\Split $split = null,
        Interfaces\Merchant $merchant = null,
        $idempotencyKey = null
    ) {
        return $this->refund(
            (new Structures\Refund())
                ->setOriginalTransaction($originalTransaction)
                ->setMerchant($merchant ? $merchant : $originalTransaction->merchant())
                ->setAmount($amount)
                ->setSplit($split),
            $idempotencyKey
        );
    }

    public function sale(
        Interfaces\Sale $sale,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($sale);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->sale($transaction);
    }

    public function saleWithAccountDetails(
        Interfaces\Account $account,
        Interfaces\AccountHolder $accountHolder,
        Interfaces\Merchant $merchant,
        $amount,
        Interfaces\Customer $customer = null,
        Interfaces\Split $split = null,
        $idempotencyKey = null,
        $currency = null
    ) {
        return $this->sale(
            (new Structures\Sale())
                ->setAccount($account)
                ->setAccountHolder($accountHolder)
                ->setMerchant($merchant)
                ->setAmount($amount)
                ->setCurrency($currency ?: self::$gateway->currency())
                ->setCustomer($customer)
                ->setSplit($split),
            $idempotencyKey
        );
    }

    public function saleWithPaymentMethod(
        Interfaces\PaymentMethod $paymentMethod,
        Interfaces\Merchant $merchant,
        $amount,
        Interfaces\Split $split = null,
        $idempotencyKey = null,
        $currency = null
    ) {
        return $this->sale(
            (new Structures\Sale())
                ->setPaymentMethod($paymentMethod)
                ->setMerchant($merchant)
                ->setAmount($amount)
                ->setCurrency($currency ?: self::$gateway->currency())
                ->setSplit($split),
            $idempotencyKey
        );
    }

    public function saleWithMasterPass(
        $masterPassTransactionId,
        Interfaces\Merchant $merchant,
        $amount,
        Interfaces\Customer $customer = null,
        Interfaces\Split $split = null,
        $idempotencyKey = null,
        $currency = null
    ) {
        return $this->sale(
            (new Structures\Sale())
                ->setMasterPassTransactionId($masterPassTransactionId)
                ->setMerchant($merchant)
                ->setAmount($amount)
                ->setCustomer($customer)
                ->setCurrency($currency ?: self::$gateway->currency())
                ->setSplit($split),
            $idempotencyKey
        );
    }

    public function saleWithToken(
        Interfaces\Token $token,
        Interfaces\Merchant $merchant,
        $amount,
        Interfaces\Split $split = null,
        $idempotencyKey = null,
        $currency = null
    ) {
        return $this->sale(
            (new Structures\Sale())
                ->setToken($token)
                ->setMerchant($merchant)
                ->setAmount($amount)
                ->setCurrency($currency ?: self::$gateway->currency())
                ->setSplit($split),
            $idempotencyKey
        );
    }

    public function voidTransaction(
        Interfaces\VoidTransaction $void,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($void);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->voidTransaction($transaction);
    }

    public function voidWithOriginalTransaction(
        Interfaces\Transaction $originalTransaction,
        Interfaces\Merchant $merchant = null,
        $idempotencyKey = null
    ) {
        return $this->voidTransaction(
            (new Structures\VoidTransaction())
                ->setOriginalTransaction($originalTransaction)
                ->setMerchant($merchant ? $merchant : $previousTransaction->merchant()),
            $idempotencyKey
        );
    }

    public function credit(
        Interfaces\Credit $credit,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($credit);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->credit($transaction);
    }

    public function creditWithPaymentMethod(
        Interfaces\PaymentMethod $paymentMethod,
        Interfaces\Merchant $merchant,
        $amount,
        $currency = null,
        $idempotencyKey = null
    ) {
        return $this->credit(
            (new Structures\Credit())
                ->setMerchant($merchant)
                ->setPaymentMethod($paymentMethod)
                ->setAmount($amount)
                ->setCurrency($currency ?: self::$gateway->currency()),
            $idempotencyKey
        );
    }

    public function merchantRates(
        Interfaces\Merchant $merchant = null,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction(
            $merchant ? $merchant : new Structures\Merchant()
        );

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->merchantRates($transaction);
    }

    public function merchantLimits(
        Interfaces\Merchant $merchant,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($merchant);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->merchantLimits($transaction);
    }

    public function generateMerchantLink(
        Interfaces\Merchant $merchant,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($merchant);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->generateMerchantLink($transaction);
    }

    public function generateHostedPageAccessToken(
        Interfaces\Merchant $merchant,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($merchant);
        $transaction->idempotencyKey($idempotencyKey);
        return self::$gateway->generateHostedPageAccessToken($transaction);
    }

    public function createScheduledTransaction(
        Interfaces\ScheduledTransaction $scheduledTransaction,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($scheduledTransaction);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->createScheduledTransaction($transaction);
    }

    public function getScheduledTransaction(
        Interfaces\ScheduledTransaction $scheduledTransaction,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($scheduledTransaction);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->getScheduledTransaction($transaction);
    }

    public function deleteScheduledTransaction(
        Interfaces\ScheduledTransaction $scheduledTransaction,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($scheduledTransaction);

        $transaction->idempotencyKey($idempotencyKey);

        return self::$gateway->deleteScheduledTransaction($transaction);
    }

    public function getDailyScheduledTransaction(
        Interfaces\PaginatedScheduledTransactions $paginatedScheduledTransactions,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($paginatedScheduledTransactions);
        $transaction->idempotencyKey($idempotencyKey);
        return self::$gateway->getDailyScheduledTransaction($transaction);
    }

    public function copyPaymentPlan(
        Interfaces\PaymentPlan $paymentPlan,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($paymentPlan);
        $transaction->idempotencyKey($idempotencyKey);
        return self::$gateway->copyPaymentPlan($transaction);
    }

    public function editPaymentPlan(
        Interfaces\PaymentPlan $paymentPlan,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($paymentPlan);
        $transaction->idempotencyKey($idempotencyKey);
        return self::$gateway->editPaymentPlan($transaction);
    }
    
    public function getMerchantPaymentPlans(
        Interfaces\PaginatedPaymentPlans $paginatedPaymentPlans,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($paginatedPaymentPlans);
        $transaction->idempotencyKey($idempotencyKey);
        return self::$gateway->getMerchantPaymentPlans($transaction);
    }
    
    public function getDefaultPaymentPlans(
        Interfaces\MultiplePaymentPlans $plans,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($plans);
        $transaction->idempotencyKey($idempotencyKey);
        return self::$gateway->getDefaultPaymentPlans($transaction);
    }
    
    public function createPaymentPlanSubscription(
        Interfaces\Subscription $Subscription,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($Subscription);
        $transaction->idempotencyKey($idempotencyKey);
        return self::$gateway->createPaymentPlanSubscription($transaction);
    }

    public function editPaymentPlanSubscription(
        Interfaces\Subscription $Subscription,
        $idempotencyKey = null
    ) {
        $transaction = new Transactions\IdempotentTransaction($Subscription);
        $transaction->idempotencyKey($idempotencyKey);
        return self::$gateway->editPaymentPlanSubscription($transaction);

    }
}
