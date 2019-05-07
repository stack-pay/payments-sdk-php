<?php

use StackPay\Payments\Requests\v1\ScheduledTransactionRequest;
use StackPay\Payments\Requests\v1\PaginatedScheduledTransactionRequest;
use StackPay\Payments\Structures;
use StackPay\Payments\Translators;

class ScheduledTransactionRequestUnitTest extends UnitTestCase
{
    public function setUp()
    {
        $this->StackPay                                     = new StackPay\Payments\StackPay('public-12345', 'private-12345');

        $this->merchant                                     = new Structures\Merchant(13, 'merchant_hash_key_123');

        $this->scheduledTransaction                         = new Structures\ScheduledTransaction;
        $this->scheduledTransaction->id                     = 12345;
        $this->scheduledTransaction->merchant               = $this->merchant;
        $this->scheduledTransaction->scheduledAt            = new DateTime('2016-01-01', new DateTimeZone('EST'));

        $this->paginatedScheduledTransaction                = new Structures\PaginatedScheduledTransactions;
        $this->paginatedScheduledTransaction->beforeDate    = new DateTime('2016-01-01', new DateTimeZone('EST'));
        $this->paginatedScheduledTransaction->afterDate     = new DateTime('2016-01-01', new DateTimeZone('EST'));


        $this->request = new ScheduledTransactionRequest($this->scheduledTransaction);
        $this->paginatedRequest = new PaginatedScheduledTransactionRequest($this->paginatedScheduledTransaction);
    }

    public function testConstructor()
    {
        $this->assertEquals($this->request->scheduledTransaction, $this->scheduledTransaction);
    }

    public function testCreate()
    {
        $mockedScheduledTransactionElement = [
            'fakeKey1' => 'fakeValue1',
            'fakeKey2' => 'fakeValue2',
            'fakeKey3' => 'fakeValue3',
        ];

        $mockedRestTranslator = Mockery::mock(Translators\V1RESTTranslator::class);
        $mockedRestTranslator->shouldReceive('buildScheduledTransactionElement')->once()
            ->with($this->scheduledTransaction)
            ->andReturn($mockedScheduledTransactionElement);

        $this->request->restTranslator = $mockedRestTranslator;

        $createRequest = $this->request->create();

        $this->assertEquals($createRequest->method, 'POST');
        $this->assertEquals($createRequest->endpoint, '/api/scheduled-transactions');
        $this->assertEquals($createRequest->hashKey, $this->merchant->hashKey);
        $this->assertEquals($createRequest->body, $mockedScheduledTransactionElement);
    }

    public function testDelete()
    {
        $deleteRequest = $this->request->delete();

        $this->assertEquals($deleteRequest->method, 'DELETE');
        $this->assertEquals($deleteRequest->endpoint, '/api/scheduled-transactions/'. $this->scheduledTransaction->id);
        $this->assertEquals($deleteRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertNull($deleteRequest->body);
    }

    public function testGet()
    {
        $getRequest = $this->request->get();

        $this->assertEquals($getRequest->method, 'GET');
        $this->assertEquals($getRequest->endpoint, '/api/scheduled-transactions/'. $this->scheduledTransaction->id);
        $this->assertEquals($getRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertNull($getRequest->body);
    }

    public function testRetry()
    {
        $retryRequest = $this->request->retry();

        $this->assertEquals($retryRequest->method, 'POST');
        $this->assertEquals($retryRequest->endpoint, '/api/scheduled-transactions/'. $this->scheduledTransaction->id .'/attempts');
        $this->assertEquals($retryRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertNull($retryRequest->body);
    }

    public function testRetryWithPaymentMethod()
    {
        // attach a paymentMethod
        $paymentMethod      = new Structures\PaymentMethod;
        $paymentMethod->id  = 12345;

        $this->request->scheduledTransaction->paymentMethod = $paymentMethod;

        $mockedPaymentMethodElement = [
            'fakeKey1' => 'fakeValue1',
            'fakeKey2' => 'fakeValue2',
            'fakeKey3' => 'fakeValue3',
        ];

        $mockedRestTranslator = Mockery::mock(Translators\V1RESTTranslator::class);
        $mockedRestTranslator->shouldReceive('buildPaymentMethodElement')->once()
            ->with($this->scheduledTransaction->paymentMethod)
            ->andReturn($mockedPaymentMethodElement);

        $this->request->restTranslator = $mockedRestTranslator;

        $retryRequest = $this->request->retry();

        $this->assertEquals($retryRequest->method, 'POST');
        $this->assertEquals($retryRequest->endpoint, '/api/scheduled-transactions/'. $this->scheduledTransaction->id .'/attempts');
        $this->assertEquals($retryRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertEquals($retryRequest->body, ['payment_method' => $mockedPaymentMethodElement]);
    }

    public function testGetDailyScheduledTransactions()
    {
        $getRequest = $this->paginatedRequest->getDailyScheduledTransactions();

        $this->assertEquals($getRequest->method, 'GET');
        $this->assertEquals($getRequest->endpoint, '/api/scheduled-transactions?createdBetween='. $this->paginatedScheduledTransaction->beforeDate->format('Y-m-d').','. $this->paginatedScheduledTransaction->afterDate->format('Y-m-d'));
        $this->assertEquals($getRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertNull($getRequest->body);

        $this->paginatedScheduledTransaction->status        = 'scheduled';
        $getRequest = $this->paginatedRequest->getDailyScheduledTransactions();
        $this->assertEquals($getRequest->method, 'GET');
        $this->assertEquals($getRequest->endpoint, '/api/scheduled-transactions?createdBetween='. $this->paginatedScheduledTransaction->beforeDate->format('Y-m-d').','. $this->paginatedScheduledTransaction->afterDate->format('Y-m-d').'&status='. $this->paginatedScheduledTransaction->status);
        $this->assertEquals($getRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertNull($getRequest->body);

        $this->paginatedScheduledTransaction->perPage       = '1';
        $this->paginatedScheduledTransaction->currentPage   = '10';
        $getRequest = $this->paginatedRequest->getDailyScheduledTransactions();
        $this->assertEquals($getRequest->method, 'GET');
        $this->assertEquals($getRequest->endpoint, '/api/scheduled-transactions?createdBetween='. $this->paginatedScheduledTransaction->beforeDate->format('Y-m-d').','. $this->paginatedScheduledTransaction->afterDate->format('Y-m-d').'&status='. $this->paginatedScheduledTransaction->status.'&per_page='. $this->paginatedScheduledTransaction->perPage.'&page='. $this->paginatedScheduledTransaction->currentPage);
        $this->assertEquals($getRequest->hashKey, $this->StackPay::$privateKey);
        $this->assertNull($getRequest->body);
    }
}
