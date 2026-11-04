<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgeRestrictedSetting extends Model
{
    protected $table = 'age_restricted_settings_tables';

    protected $fillable = [
        'id',
        'age',
        'top_content',
        'body_content',
    ];

    const EIGHTEEN_AND_ABOVE = 18;
    const TWENTYONE_AND_ABOVE = 21;


}
