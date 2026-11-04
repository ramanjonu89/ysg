<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BusinessSetting
 *
 * @property int $id
 * @property string $type
 * @property string|null $value
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereValue($value)
 * @mixin \Eloquent
 */
class BusinessSetting extends Model
{
    const REFERRAL_TYPE_NORMAL = 1;
    const REFERRAL_TYPE_CORPORATE = 2;
    const CASH_PAYMENT_TYPE = 'cash_payment';

    protected $fillable = [
        'type',
        'value'
    ];
}
