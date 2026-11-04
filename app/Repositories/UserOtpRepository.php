<?php
namespace App\Repositories;

use App\Models\UserOtp;

class UserOtpRepository extends AbstractRepo
{
    const PER_PAGE = 15;
    public function getModel(): string
    {
        return UserOtp::class;
    }

    public function getList($params)
    {
        $query = $this->model->query();
        $query->orderBy('created_at', 'desc');

        if (!empty($params['count'])) {
            $query->where('count', $params['count']);
        }

        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function($query) use ($keyword) {
                $query->where('username', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }
        $perPage = $params['perPage'] ?? self::PER_PAGE;

        return $query->paginate($perPage);
    }
}
