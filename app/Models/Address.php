<?php
namespace App\Models;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    const SET_DEFAULT_ADDRESS = 1;
    const COUNTRY_CODE_SG = '+65';

    protected $fillable = [
        'user_id',
        'address',
        'country',
        'city',
        'postal_code',
        'phone',
        'country_id',
        'city_id',
        'title',
        'set_default',
        'country_code'
    ];

    public function cityRelationship()
    {
        return $this->hasOne(City::class,'id','city_id');
    }

    public function countryRelationship()
    {
        return $this->hasOne(Country::class,'id','country_id');
    }

    public function fullAddress()
    {
        return $this->address . ', ' . ($this->cityRelationship ? $this->cityRelationship->name : 'N/A') . ', ' . ($this->countryRelationship ? $this->countryRelationship->name : 'N/A') . ', ' . $this->postal_code;
    }

    public function fullPhone()
    {
        return $this->country_code . $this->phone;
    }
}
