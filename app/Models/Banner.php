<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Banner
 *
 * @property int $id
 * @property int $index
 * @property string|null $template
 * @property string|null $photo
 * @property string|null $url
 * @property int $position
 * @property int $published
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereUrl($value)
 * @mixin \Eloquent
 */
class Banner extends Model
{
    protected $fillable = [
        'id',
        'index',
        'template',
        'photo',
        'url',
        'position',
        'published',
        'start_date',
        'end_date'
    ];

    const BANNER_POSITION_1 = 1;
    const BANNER_POSITION_2 = 2;

    const START_DATE_ONLY = 'start_date_only';
    const END_DATE_ONLY   = 'end_date_only';

    public static function getTemplateList(): array
    {
        return [
            '1-col' => __('1 col width'),
            '2-col' => __('2 col width'),
            '3-col' => __('3 col width'),
        ];
    }

    public function isExpired() {
        $now = strtotime(date('d-m-Y H:i:s'));

        if (empty($this->start_date) && empty($this->end_date)) {
            return false;
        } 

        if (!empty($this->start_date) && !empty($this->end_date)) {
            return  $now < $this->start_date || 
                    $now >= $this->end_date;

        } elseif (!empty($this->start_date)) {
            return $now <= $this->start_date;

        } elseif (!empty($this->end_date)) {
            return $now >= $this->end_date;
        }

        return false;
    }
}
