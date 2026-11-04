<?php
namespace App\Repositories\CustomPage;

use App\Models\CustomPage\Page;
use App\Repositories\AbstractRepo;

class PageRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Page::class;
    }

    public function getList($params)
    {
        $query = $this->model::query();
        $query->where('type', '!=', Page::TYPE_HOMEPAGE);

        if (isset($params['notIn'])) {
            $query->whereNotIn('id', $params['notIn']);
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function getOne($params)
    {
        $query = $this->model::query();

        $query->where('type', '!=', Page::TYPE_HOMEPAGE);

        if (isset($params['is_enabled'])) {
            $query->where('is_enabled', '=', $params['is_enabled']);
        }
        if (isset($params['slug'])) {
            $query->where('slug', '=', $params['slug']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->first();
    }
}
