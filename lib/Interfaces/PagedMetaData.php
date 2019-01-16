<?php

namespace StackPay\Payments\Interfaces;

interface PagedMetaData
{
    public function page();
    public function perPage();
    public function total();
    public function currentPage();
    public function totalPages();
    public function links();

    //-----------

    public function setPage($page = 1);
    public function setPerPage($perPage = 10);
    public function setTotal($total = null);
    public function setCurrentPage($currentPage = null);
    public function setTotalPages($totalPages = null);
    public function setLinks($links = null);
}
