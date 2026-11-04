<?php
namespace App\Repositories;

use App\Models\MicrositeUser;

class MicrositeUserRepository extends AbstractRepo
{

    public function getModel(): string
    {
        return MicrositeUser::class;
    }

    public function getList()
    {
        $query = $this->model->query();
        $query->orderBy('created_at', 'desc');

        return $query->get();
    }
}
