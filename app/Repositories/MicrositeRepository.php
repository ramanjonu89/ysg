<?php
namespace App\Repositories;

use App\Models\Microsite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MicrositeRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Microsite::class;
    }

    /**
     * get list of seller
     * @param array $params
     * @return mixed
     */
    public function getList(array $params)
    {
        return $this->getListQuery($params)->get();
    }

    public function getListQuery(array $params)
    {
        $active = $params['active'] ?? null;

        $query = $this->model::with(['seller', 'seller.user']);

        if ($active != null) {
            $query->where('active', $active);
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query;
    }

    public function toggleActive(int $id): bool
    {
        $entity = $this->model::find($id);
        $entity->active = !$entity->active;
        return $entity->save();
    }

    public function getArrayUserIdHasActiveMicrosite()
    {
        $params = [
            'active' => true,
            'page' => 0
        ];

        $query = $this->getListQuery($params);

        return $query->pluck('seller.user.id');
    }

    public function getMicrositeUserIds()
    {
        return DB::table('microsite_users')->pluck('user_id')->toArray();
    }
    
}
