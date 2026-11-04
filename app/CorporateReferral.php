<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CorporateReferral extends Model
{
    protected $table = 'corporate_referral';
    protected $fillable = [
        'domain',
        'link',
        'share_limit',
        'expire_after',
        'expire_time_unit'
    ];
}

