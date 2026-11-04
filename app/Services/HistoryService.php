<?php
namespace App\Services;

use App\Repositories\HistoryRepository;

class HistoryService
{
    protected $user;
    protected $repo;

    public function __construct(HistoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getList($params = [])
    {
        return  $this->repo->getList($params);
    }

    public function getListForExport($params = [])
    {
        return  $this->repo->getListForExport($params);
    }
}