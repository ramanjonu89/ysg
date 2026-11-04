<?php
namespace App\Services;

trait SortableTrait {
    /**
     * Update index of model.
     *
     * @param array $list
     * @return int
     */
    public function updateIndex(array $list): int
    {
        return  $this->repo->updateIndex($list);
    }
}