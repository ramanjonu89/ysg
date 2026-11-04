<?php
namespace App\Repositories\Api\Mobile;

use App\Models\Review;
use App\Repositories\AbstractRepo;

class ReviewRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    protected $sharedRepo;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function getModel(): string
    {
        return Review::class;
    }

    /**
     * get category by id
     * @param int $id
     * @return Review|null
     */
    public function get(int $id)
    {
        return $this->sharedRepo->find($id);
    }

    public function list($productId = null, $userId = null, $sortBy, $showOption , $perPage, $page)
    {
        $query = $this->model::query();
        // get user name by join users table
        $query->leftJoin('users', 'reviews.user_id', '=', 'users.id');
        $query->select('reviews.*', 'users.name as user_name', 'users.avatar as user_avatar');


        if ($productId) {
            $query->where('product_id', $productId);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $query->where('status', 1);

        if ($showOption == 'only_1') {
            $query->where('reviews.rating', '=', 1);
        } elseif ($showOption == 'only_2') {
            $query->where('reviews.rating', '=', 2);
        } elseif ($showOption == 'only_3') {
            $query->where('reviews.rating', '=', 3);
        } elseif ($showOption == 'only_4') {
            $query->where('reviews.rating', '=', 4);
        } elseif ($showOption == 'only_5') {
            $query->where('reviews.rating', '=', 5);
        }

        if ($sortBy == 'newest') {
            $query->orderBy('reviews.created_at', 'desc');
        } elseif ($sortBy == 'oldest') {
            $query->orderBy('reviews.created_at', 'asc');
        } elseif ($sortBy == 'high_to_low') {
            $query->orderBy('reviews.rating', 'desc');
        } elseif ($sortBy == 'low_to_high') {
            $query->orderBy('reviews.rating', 'asc');
        } else {
            $query->orderBy('reviews.created_at', 'desc');
        }
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
