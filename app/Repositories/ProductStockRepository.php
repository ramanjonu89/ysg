<?php
namespace App\Repositories;

use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductStockRepository extends AbstractRepo
{
    
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return ProductStock::class;
    }
}
