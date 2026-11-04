<?php
namespace App\Repositories;

use App\Models\CustomerProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CustomerProductRepository extends AbstractRepo
{
    
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return CustomerProduct::class;
    }

}
