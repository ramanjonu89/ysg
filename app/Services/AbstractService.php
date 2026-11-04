<?php

namespace App\Services;

use App\Repositories\Irepo;
use Illuminate\Http\Request;

abstract class AbstractService {
    protected $repo;

    public function __construct(IRepo $repo) {
        $this->repo = $repo;
    }

    public function get($id) {
        return $this->repo->find($id);
    }

    public function findBy($column, $value) {
        return $this->repo->findBy($column, $value);
    }

    public function getAll() {
        return $this->repo->getAll();
    }

    public function create($data) {
        return $this->repo->create($data);
    }

    public function update($id, $data) {
        return $this->repo->update($id, $data);
    }

    public function updateByRequest($id, Request $request) {
        return $this->repo->update($id, $request->all());
    }

    public function destroy($id) {
        return $this->repo->destroy($id);
    }

    public function getAllWithConditions($conditions = [], $column = ['*']) {
        return $this->repo->getAllWithConditions($conditions, $column);
    }

    public function getFirstWithConditions($conditions = [], $column = ['*']) {
        return $this->repo->getFirstWithConditions($conditions, $column);
    }

    public function getDataLimitWithConditions($conditions = [], $column = ['*'], $limit) {
        return $this->repo->getDataLimitWithConditions($conditions, $column, $limit);
    }

    public function getFirst(){
        return $this->repo->getFirst();
    }

    public function getByIds($ids) {
        return $this->repo->getByIds($ids);
    }

}