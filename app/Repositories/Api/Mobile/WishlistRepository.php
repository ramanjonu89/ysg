<?php
namespace App\Repositories\Api\Mobile;

use App\Models\Wishlist;
use App\Repositories\AbstractRepo;

class WishlistRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return Wishlist::class;
    }

    public function getListByUser($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->whereHas('product', function ($query) {
                $query->where('published', true);
            })->get();
    }

}