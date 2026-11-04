<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Link
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Link query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Link whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Link whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Link whereUrl($value)
 * @mixin \Eloquent
 */
class Link extends Model
{
    public function linkTranslation($languageId = 1){
        return @$this->hasOne(LinkTranslation::class)->where(['language_id' => $languageId])->first();
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(LinkTranslation::class)->where('language_id', $languageId);
    }

    public function getTranslationNameAttribute()
    {
        return $this->translation->isEmpty() ? $this->name : $this->translation->first()->name;
    }
}
