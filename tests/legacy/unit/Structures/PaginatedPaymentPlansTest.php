<?php

use StackPay\Payments\Structures;

final class PaginatedPaymentPlanTest extends StructureTestCase
{
    protected $struct = StackPay\Payments\Structures\PaginatedPaymentPlans::class;

    public function test_merchant()
    {
        $this->full('merchant', Structures\Merchant::class, false);
    }

    public function test_plans()
    {
        $this->full('plans', 'array', false);
    }

    public function test_total()
    {
        $this->full('total', 'int', false);
    }

    public function test_count()
    {
        $this->full('count', 'int', false);
    }

    public function test_perPage()
    {
        $this->full('perPage', 'int', false);
    }

    public function test_currentPage()
    {
        $this->full('currentPage', 'int', false);
    }

    public function test_totalPages()
    {
        $this->full('totalPages', 'int', false);
    }

    public function test_links()
    {
        $this->full('links', 'array', false);
    }    
}
