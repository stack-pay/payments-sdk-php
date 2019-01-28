<?php

namespace StackPay\Payments\Gateways;

use Curl\Curl;

use StackPay\Payments\Currency;
use StackPay\Payments\Modes;
use StackPay\Payments\Providers;
use StackPay\Payments\URLs;

abstract class Gateway
{
    protected $apiVersion = 'v1';

    protected $baseURL;

    protected $curlProvider;

    protected $client;

    protected $currency;

    protected $mode;

    protected $privateKey;

    protected $publicKey;

    public function __construct($publicKey, $privateKey)
    {
        $this->baseURL      = URLs::PRODUCTION;
        $this->curlProvider = new Providers\CurlProvider();
        $this->currency     = Currency::USD;
        $this->mode         = Modes::PRODUCTION;

        $this->privateKey   = $privateKey;
        $this->publicKey    = $publicKey;
    }

    public function enableTestMode()
    {
        $this->mode     = Modes::DEVELOPMENT;
        $this->baseURL  = URLs::DEVELOPMENT;

        return $this;
    }

    public function baseURL($baseURL = null)
    {
        if ($baseURL) {
            $this->baseURL = $baseURL;
        }

        return $this->baseURL;
    }

    public function client($client = null)
    {
        if ($client) {
            $this->client = $client;
        }

        return $this->client;
    }

    public function currency($currency = null)
    {
        if ($currency) {
            $this->currency = $currency;
        }

        return $this->currency;
    }

    public function curlProvider(Providers\CurlProviderInterface $curlProvider = null)
    {
        if ($curlProvider) {
            $this->curlProvider = $curlProvider;
        }

        return $this->curlProvider;
    }

    protected function setHeaders($curl, $headers)
    {
        foreach ($headers as $key => $value) {
            $curl->setHeader($key, $value);
        }
    }

    protected function sendRequest($transaction, $method = 'post')
    {
        $this->curlProvider->newCurl();
        $this->setHeaders($this->curlProvider, $transaction->request()->headers());
        $transaction->request()->lock();

        switch (strtolower($method)) {
            case 'patch':
                $response = $this->curlProvider->patch(
                    $this->baseURL ."/". $transaction->request()->endpoint(),
                    $transaction->request()->body()
                );
                break;

            case 'post':
                $response = $this->curlProvider->post(
                    $this->baseURL ."/". $transaction->request()->endpoint(),
                    $transaction->request()->body()
                );
                break;

            case 'get':
                $response = $this->curlProvider->get(
                    $this->baseURL ."/". $transaction->request()->endpoint(),
                    $transaction->request()->body()
                );
                break;

            case 'delete':
                $response = $this->curlProvider->delete(
                    $this->baseURL ."/". $transaction->request()->endpoint()
                );
                break;

            default:
                throw new \Exception('Invalid HTTP method selected for request.');
        }

        $transaction->response()->raw($response);
        $transaction->response()->headers($this->curlProvider->responseHeaders());
        $transaction->response()->httpCode($this->curlProvider->httpStatusCode());
    }

    public function getIfExists($array, $key)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
    }

    abstract public function createToken($transaction);
    abstract public function createPaymentMethod($transaction);

    abstract public function auth($transaction);
    abstract public function capture($transaction);
    abstract public function sale($transaction);
    abstract public function voidTransaction($transaction);
    abstract public function refund($transaction);
    abstract public function credit($transaction);

    abstract public function merchantRates($transaction);
    abstract public function merchantLimits($transaction);
    abstract public function generateMerchantLink($transaction);

    abstract public function createScheduledTransaction($transaction);
    abstract public function getScheduledTransaction($transaction);
    abstract public function deleteScheduledTransaction($transaction);
    abstract public function getDailyScheduledTransaction($transaction);

    abstract public function copyPaymentPlan($transaction);
    abstract public function getMerchantPaymentPlans($transaction);
    abstract public function getDefaultPaymentPlans($transaction);

    abstract public function createPaymentPlanSubscription($transaction);
}
