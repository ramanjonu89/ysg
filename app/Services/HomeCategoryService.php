<?php
namespace App\Services;

use App\Repositories\HomeCategoryRepository;

class HomeCategoryService extends AbstractService
{
    protected $repo;

    public function __construct(HomeCategoryRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function getList()
    {
        return $this->repo->getAll();
    }
}