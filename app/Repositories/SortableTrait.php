<?php
namespace App\Repositories;

trait SortableTrait {
    /**
     * Update index of model.
     *
     * @param array $list
     * @return int
     */
    public function updateIndex(array $list): int
    {
        $ids = array_keys($list);
        $query = $this->model::query();
        $models = $query->whereIn('id', $ids)->get();

        $count = 0;
        foreach ($models as $model) {
            $model->index = $list[$model->id];
            if ($model->save()) {
                $count ++;
            }
        }
        return $count;
    }
}