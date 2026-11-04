<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public function childrens()
    {
        return $this->hasMany(City::class,'country_id','id');
    }
}
