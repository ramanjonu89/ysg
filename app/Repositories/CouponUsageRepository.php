<?php
namespace App\Repositories;

use App\Models\CouponUsage;
use Illuminate\Support\Carbon;

class CouponUsageRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return CouponUsage::class;
    }
}
