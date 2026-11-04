<?php
namespace App\Repositories;

interface IRepo
{
    public function find($id);

    public function create($data);

    public function update($id, $data);

    public function destroy($id);

    public function getModel();

    public function getAll();

    public function findBy($column, $value);

    public function getAllWithConditions($conditions, $column);
  
    public function findByConditions($conditions);

    public function getFirstWithConditions($conditions, $column);

    public function getDataLimitWithConditions($conditions, $column, $limit);
  
    public function getFirst();

}