<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    const REFERRAL_TYPE_NORMAL = 1;
    const REFERRAL_TYPE_CORPORATE = 2;
    protected $fillable = [
        'type',
        'value'
    ];
}
