<?php
namespace App\Repositories;

use App\Models\HomeCategory;

class HomeCategoryRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return HomeCategory::class;
    }
}