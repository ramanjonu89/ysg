<?php
namespace App\Repositories;

use App\Models\History;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HistoryRepository extends AbstractRepo
{
    use SortableTrait;

    const PER_PAGE = 20;

    public function getModel(): string
    {
        return History::class;
    }

    public function getList($params)
    {
        $query = $this->buildListQuery();

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Update index of model.
     *
     * @param $id
     * @return bool
     */
    public function togglePublish($id): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->published = !$model->published;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }

    public function getListForExport($params)
    {
        $query = $this->buildListQuery();

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            $query->limit($perPage);
        }

        return $query->get();
    }

    public function buildListQuery()
    {
        return DB::table('histories')
        ->leftJoin('products as historiable', function($join) {
            $join->on('historiable.id', '=', 'histories.historiable_id')
                ->where('histories.historiable_type', '=', Product::class);
        })
        ->leftJoin('users', 'histories.user_id', '=', 'users.id')
        ->select(
            'histories.id',
            'histories.action',
            'histories.module_name',
            'histories.historiable_id',
            'histories.created_at',
            'histories.new_value',
            'historiable.name as name',
            'users.name as user_name',
            'users.deleted_at as user_deleted_at',
            'users.user_type as user_type',
        )
        ->orderBy('created_at', 'desc')
        ;
    }
}
