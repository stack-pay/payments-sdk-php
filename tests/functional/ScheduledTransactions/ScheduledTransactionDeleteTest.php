<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;

class ScheduledTransactionDeleteTest extends ScheduledTransactionTestCase
{
    public function testFound()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->emptyResponse());

        // set scheduledTransaction details
        $scheduledTransaction       = new Structures\ScheduledTransaction;
        $scheduledTransaction->id   = 123;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->get();

        $this->response = $request->send();

        $this->assertEmptyResponse();
    }

    public function testNotFound()
    {
        // mock API success response
        $this->mockApiResponse(404, $this->notFoundResponse());

        // set scheduledTransaction details
        $scheduledTransaction       = new Structures\ScheduledTransaction;
        $scheduledTransaction->id   = 123;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->get();

        $this->response = $request->send();

        $this->assertResourceNotFoundResponse();
    }
}
