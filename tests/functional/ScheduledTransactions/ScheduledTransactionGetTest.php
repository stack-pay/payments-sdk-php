<?php

use StackPay\Payments\Requests;
use StackPay\Payments\Structures;

class ScheduledTransactionGetTest extends ScheduledTransactionTestCase
{
    public function testFound()
    {
        // mock API success response
        $this->mockApiResponse(200, $this->resourceResponse());

        // set scheduledTransaction details
        $scheduledTransaction       = new Structures\ScheduledTransaction;
        $scheduledTransaction->id   = 123;

        $request = (new Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->get();

        $this->response = $request->send();

        $this->assertResourceResponse();
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
