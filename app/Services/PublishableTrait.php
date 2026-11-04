<?php
namespace App\Services;

trait PublishableTrait {
    /**
     * Update index of model.
     *
     * @param int $id
     * @return bool
     */
    public function togglePublish(int $id): bool
    {
        return  $this->repo->togglePublish($id);
    }
}