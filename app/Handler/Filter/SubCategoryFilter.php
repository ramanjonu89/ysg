<?php

namespace App\Handler\Filter;

class SubCategoryFilter extends FilterAbstract
{
    public function initField()
    {
        $this->field = 'subcategory';
    }

    public function buildQuery($query)
    {
        if ($this->isApplied()) {
            $query->where('sc.slug', $this->value);
        }

        return $query;
    }
}