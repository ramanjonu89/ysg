<?php

namespace App\Handler\Filter;

class CategoryFilter extends FilterAbstract
{
    public function initField()
    {
        $this->field = 'category';
    }

    public function buildQuery($query)
    {
        if ($this->isApplied()) {
            $query->where('c.slug', $this->value);
        }

        return $query;
    }
}