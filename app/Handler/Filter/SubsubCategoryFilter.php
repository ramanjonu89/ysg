<?php

namespace App\Handler\Filter;

class SubsubCategoryFilter extends FilterAbstract
{
    public function initField()
    {
        $this->field = 'subsubcategory';
    }

    public function buildQuery($query)
    {
        if ($this->isApplied()) {
            $query->where('ssc.slug', $this->value);
        }

        return $query;
    }
}