<?php

namespace App\Handler\Filter;

use App\Models\Product;

class KeywordFilter extends FilterAbstract
{
    public function initField()
    {
        $this->field = 'q';
    }

    public function buildQuery($query)
    {
        $value = $this->value;
        if ($this->isApplied()) {
            $query->where(function($sq) use ($value) {
                $sq->where('p.name', 'like', '%' . $value . '%')
                    ->orWhere('p.tags', 'like', '%' . $value . '%')
                    ->orWhere('p.unit', 'like', '%' . $value . '%');
            });
        }

        return $query;
    }
}