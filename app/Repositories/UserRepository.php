<?php
namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\DB;

class UserRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return User::class;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getCompanies()
    {
        $query = $this->model->query();

        $query->whereNotNull('company_id');
        $query->where('company_id', '<>', "");

        return $query->get();
    }

    public function getFlagShipUserIdsArray(): array
    {
        return DB::table('sellers')
            ->join('users', 'sellers.user_id', '=', 'users.id')
            ->where('sellers.verification_status', '=', 1)
            ->where('users.company_type', User::COMPANY_TYPE_FLAG_SHIP)
            ->pluck('users.id')
            ->toArray();
    }

    public function getUsersByDomain(string $domain): \Illuminate\Support\Collection
    {
        return DB::table('users as u')
            ->select('u.id', 'u.email', DB::raw('SUM(uca.is_counted) as shared'))
            ->leftjoin('user_corporate_access as uca', 'u.id', '=', 'uca.parent_id')
            ->where('u.email', 'like', '%' . $domain)
            ->groupBy('u.id')
            ->get();
    }

    public function getSellerFilter($validProducts)
    {
        $validUserIds = $validProducts
            ->select('products.user_id')
            ->get()
            ->toArray();

        if (isMicrosite()) {
            $validUserIds = [getMicrositeShop()->user->id];
        }

        $sellers = DB::table('users')
            ->leftJoin('sellers', 'users.id', 'sellers.user_id')
            ->whereIn('users.id', $validUserIds)
            ->select(['sellers.id', 'users.name', 'users.id as user_id'])
            ->orderBy('users.name')
            ->get();

        return $sellers;
    }

    /**
     * Get users by IDs in the same order as provided in the array
     */
    public function getByIdsInOrder($ids)
    {
        if (empty($ids)) {
            return collect();
        }

        // Remove duplicates while preserving the order of first occurrence
        $uniqueIds = array_values(array_unique($ids));

        // Use FIELD() to maintain the order of IDs as provided
        $placeholders = str_repeat('?,', count($uniqueIds) - 1) . '?';
        $fieldExpression = "FIELD(id, {$placeholders})";
        
        return $this->model->whereIn('id', $uniqueIds)
            ->orderByRaw($fieldExpression, $uniqueIds)
            ->get();
    }

}
