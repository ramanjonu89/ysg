<?php
namespace App\Repositories;

use App\Models\UserCorporateAccess;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserCorporateAccessRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return UserCorporateAccess::class;
    }

    public function getList($params) {

        $userId = $params['user_id'] ?? null;
        $parentId = $params['parent_id'] ?? null;
        $domain = $params['domain'] ?? null;
        $notExpire = $params['not_expire'] ?? null;
        $isActive = $params['is_active'] ?? null;

        $query = $this->model::query();

        if ($userId) {
            $query->where('user_id', '=', $userId);
        }

        if ($parentId) {
            $query->where('parent_id', '=', $parentId);
        }

        if ($domain) {
            $query->where('domain', '=', $domain);
        }

        if ($notExpire) {
            $query->where(function ($query) {
                $query->where('expire_at', '>=', Carbon::now())
                ->orWhereNull('expire_at');
            });
        }

        if ($isActive) {
            $query->where('is_active', '=', $isActive);
        }

        return $query->get();
    }

    public function getUserShareTimes(User $user)
    {
        return DB::table('user_corporate_access')
            ->where('parent_id', $user->id)
            ->sum('is_counted');
    }

    public function resetAccessPassByDomain(string $domain): int
    {
        return DB::table('user_corporate_access')
            ->where('domain', $domain)
            ->update(['is_counted' => 0])
            ;
    }

    public function resetAccessPassByUser(User $user): int
    {
        return DB::table('user_corporate_access')
            ->where('parent_id', $user->id)
            ->update(['is_counted' => 0])
            ;
    }
}
