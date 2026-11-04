<?php

namespace App\Handler\Filter;

class BrandFilter extends FilterAbstract
{
    public function initField()
    {
        $this->field = 'brand_ids';
    }

    public function buildQuery($query)
    {
        if ($this->isApplied()) {
            $query->whereIn('brand_id', $this->value);
        }

        return $query;
    }
}
