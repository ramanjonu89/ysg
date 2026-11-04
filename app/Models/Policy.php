<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Policy
 *
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Policy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Policy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Policy query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Policy whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Policy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Policy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Policy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Policy whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Policy extends Model
{
    public function policyTranslation($languageId = 1){
        return @$this->hasOne(PolicyTranslation::class)->where(['language_id' => $languageId])->first();
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(PolicyTranslation::class)->where('language_id', $languageId);
    }

    public function getTranslationContentAttribute()
    {
        return $this->translation->isEmpty() ? $this->content : $this->translation->first()->content;
    }
}
