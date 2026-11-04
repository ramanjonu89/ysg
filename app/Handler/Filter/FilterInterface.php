<?php

namespace App\Handler\Filter;

interface FilterInterface
{
    public function isApplied(): bool;
    public function buildQuery($query);
}