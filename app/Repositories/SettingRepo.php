<?php
namespace App\Repositories;

abstract class SettingRepo extends AbstractRepo
{
    public function getAll()
    {
        return $this->model::all();
    }
    public function getByKey($key) {
        $query = $this->model::query();
        return $query->where('key', $key)->first();
    }
}