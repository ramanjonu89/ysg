<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BusinessSetting
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BusinessSetting whereValue($value)
 * @mixin \Eloquent
 */
class PaymentSetting extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];
}
