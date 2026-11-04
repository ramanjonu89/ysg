<?php
namespace App\Repositories\Api\Mobile;

use App\Models\Product;
use App\Repositories\AbstractRepo;
use App\Repositories\ProductRepository as SharedProductRepository;

class ProductRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    protected $sharedRepo;

    public function __construct(SharedProductRepository $sharedRepo)
    {
        parent::__construct();
        $this->sharedRepo = $sharedRepo;
    }
    
    public function getModel(): string
    {
        return Product::class;
    }

    /**
     * get category by id
     * @param int $id
     * @return Product|null
     */
    public function get(int $id)
    {
        return $this->sharedRepo->find($id);
    }

}
