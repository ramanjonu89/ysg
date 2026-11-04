<?php

namespace App\Services;

use App\Models\NewSletter;
use App\Repositories\NewSletterRepository;

class NewSletterService extends AbstractService
{
    protected $repo;

    public function __construct(NewSletterRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getList()
    {
        return $this->repo->getList();
    }

    public function updateStatus($id)
    {
        return $this->repo->updateStatus($id);
    }

    public function hasConflict(?int $start, ?int $end, ?int $ignoreId = null): bool
    {
        return $this->repo->hasConflict($start, $end, $ignoreId);
    }
}
