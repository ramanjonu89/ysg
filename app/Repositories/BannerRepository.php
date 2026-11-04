<?php
namespace App\Repositories;

use App\Models\Banner;

class BannerRepository extends AbstractRepo
{
    use SortableTrait;

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Banner::class;
    }

    public function getList($params)
    {
        $published = $params['published'] ?? null;
        $position = $params['position'] ?? null;

        $query = $this->model->query();

        if (!is_null($published)) {
            $query->where('published', '=', $published);
        }

        if (!is_null($position)) {
            $query->where('position', '=', $position);
        }

        $query->orderBy('index', 'asc');
        $query->orderBy('created_at', 'desc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Toggle publish model.
     *
     * @param int $id
     * @return boolean
     */
    public function togglePublish(int $id): bool
    {
        $query = $this->model->query();
        $model = $query->where('id', $id)->first();
        if ($model) {
            $count = $this->getList([
                'published' => 1,
                'position' => $model->position,
            ])->count();

            $model->published = !$model->published;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }
}
