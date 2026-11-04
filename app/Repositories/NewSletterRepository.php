<?php
namespace App\Repositories;

use App\Models\NewSletter;

class NewSletterRepository extends AbstractRepo
{
    public function getModel()
    {
        return NewSletter::class;
    }

    public function getList($params = [])
    {
        $query = $this->model->query();

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        $query->orderBy('created_at', 'desc');
        return $query->get();
    }

    public function updateStatus($id)
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->status = !$model->status;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }

    public function hasConflict(?int $start, ?int $end, ?int $ignoreId = null)
    {
        return Newsletter::where('status', 1)
            ->when($ignoreId, function ($q) use ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            })
            ->where(function ($q) use ($start, $end) {

                if ($start && $end) {
                    $q->where('start_date', '<=', $end)
                    ->where(function ($q) use ($start) {
                        $q->where('end_date', '>=', $start)
                            ->orWhere('end_date', 0);
                    });
                } elseif ($start) {
                    $q->where(function ($q) use ($start) {
                        $q->where('end_date', '>=', $start)
                            ->orWhere('end_date', 0);
                    });
                } elseif ($end) {
                    $current = time();
                        $q->where('start_date', '<=', $end)
                        ->where(function ($q) use ($current) {
                            $q->where('end_date', '>=', $current)
                                ->orWhere('end_date', 0);
                    });
                }

            })
        ->exists();
    }
}
