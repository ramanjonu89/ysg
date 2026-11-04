<?php
namespace App\Repositories\CustomPage;

use App\Models\CustomPage\Block;
use App\Repositories\AbstractRepo;

class BlockRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Block::class;
    }

    public function getList(array $params = [])
    {
        $type = $params['type'] ?? null;
        $keyword  = $params['keyword'] ?? null;
        $query = $this->model::query();

        if ($type) {
            $query->where('type', $type);
        }

        if (!empty($params['page_type'])) {
            $query->where('page_type', $params['page_type']);
            switch ($params['page_type']) {
                case 'microsite':
                    $query->where('microsite_id', null);
                    break;
                case 'shop':
                    $query->where('blockable_id', 0);
                    break;
                default:
                    $query->where('ysg_page_id', null);
                    break;
            }
        }

        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function($query) use ($keyword) {
                $query->where('title', 'like', '%' . $keyword . '%');
            });
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }
}
