<?php

namespace StackPay\Payments\Interfaces;

interface Paginated
{
    public function total();
    public function count();
    public function perPage();
    public function currentPage();
    public function totalPages();
    public function links();

    // ---------

    public function setTotal($total = null);
    public function setCount($count = null);
    public function setPerPage($perPage = null);
    public function setCurrentPage($currentPage = null);
    public function setTotalPages($totalPages = null);
    public function setLinks(array $links = null);
}
