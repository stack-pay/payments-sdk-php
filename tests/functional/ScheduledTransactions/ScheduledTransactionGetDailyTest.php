<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;

class ScheduledTransactionGetDailyTest extends ScheduledTransactionTestCase
{
    public function testFound()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse());

        // set paginatedScheduledTransaction details
        $paginatedScheduledTransaction       = new Structures\PaginatedScheduledTransactions;
        $paginatedScheduledTransaction->beforeDate    = new DateTime('2016-01-01', new DateTimeZone('EST'));
        $paginatedScheduledTransaction->afterDate     = new DateTime('2016-01-01', new DateTimeZone('EST'));

        $request = (new Requests\v1\PaginatedScheduledTransactionRequest($paginatedScheduledTransaction))
            ->getDailyScheduledTransactions();

        $this->response = $request->send();

        $this->assertResourceResponse();
    }

    public function testNotFound()
    {
        // mock API success response
        $this->mockApiResponse(404, $this->notFoundResponse());

        // set paginatedScheduledTransaction details
        $paginatedScheduledTransaction       = new Structures\PaginatedScheduledTransactions;
        $paginatedScheduledTransaction->beforeDate    = new DateTime('2016-01-01', new DateTimeZone('EST'));
        $paginatedScheduledTransaction->afterDate     = new DateTime('2016-01-01', new DateTimeZone('EST'));

        $request = (new Requests\v1\PaginatedScheduledTransactionRequest($paginatedScheduledTransaction))
            ->getDailyScheduledTransactions();

        $this->response = $request->send();

        $this->assertResourceNotFoundResponse();
    }
}
