<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepo implements IRepo
{
    const PER_PAGE = 10;
    const UNLIMITED_NUMBER = 10000;

    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function find($id): Model
    {
        $item = $this->model->findOrFail($id);

        return $item;
    }

    public function findBy($column, $value): Model
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function filterBy(array $params): ?Model
    {
        $query = $this->model->query();
        foreach($params as $param) {
            $query->where($param['column'], $param['value']);
        }
        return $query->first();
    }

    public function getAllBy(array $params)
    {
        $query = $this->model->query();
        foreach($params as $param) {
            if (is_array($param['value'])) {
                $query->whereIn($param['column'], $param['value']);
            } else {
                $query->where($param['column'], $param['value']);
            }
        }
        return $query->get();
    }
    
    public function getAll()
    {
        $items = $this->model->all();

        return $items;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        $item = $this->model->find($id);
        $item->update($data);
        return $item;
    }

    public function destroy($id)
    {
        $item = $this->model->find($id);

        return $item->delete();
    }

    public function getLimit(array $params): int
    {
        $limit = intval($params['per_page']) ?? self::PER_PAGE;
        if ($this->isZero($limit)) {
            $limit = self::UNLIMITED_NUMBER;
        }
        return $limit;
    }

    protected function isZero($limit): bool
    {
        return $limit === 0;
    }

    public function getAllWithConditions($conditions, $column){
        return $this->model->where($conditions)->select($column)->get();
    }

    public function findByConditions($conditions): Model
    {
        return $this->model->where($conditions)->firstOrFail();
    }

    public function getFirstWithConditions($conditions, $column){
        return $this->model->where($conditions)->first();
    }

    public function getDataLimitWithConditions($conditions, $column, $limit){
        return $this->model->where($conditions)->take($limit)->get();
    }
    
    public function getFirst(){
        return $this->model->first();
    }

    public function getByIds($ids) {
        return $this->model->whereIn('id', $ids)->get();
    }
}