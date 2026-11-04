<?php
namespace App\Repositories;

use App\Models\Slider;

class SliderRepository extends AbstractRepo
{
    use SortableTrait;

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Slider::class;
    }

    public function getList($params)
    {
        $published = $params['published'] ?? null;

        $query = $this->model::query();

        if (!is_null($published)) {
            $query->where('published', '=', $published);
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
}
