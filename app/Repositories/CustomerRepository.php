<?php
namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CustomerRepository extends AbstractRepo
{
    
    const PER_PAGE = 20;

    public function getModel(): string
    {
        return Customer::class;
    }

    public function getList(array $params) {
        $query = DB::table('customers as c')
                    ->leftJoin('users as u', 'c.user_id', '=', 'u.id')
                    ->leftJoin('addresses as a', 'a.user_id')
                    ->orderBy('c.created_at', 'desc')
                    ;
    }
}
