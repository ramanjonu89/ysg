<?php
namespace App\Models\CustomPage;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomPage\Block
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $content
 * @property string|null $options
 * @property string|null $image
 * @property int|null $sort
 * @property string|null $css_class
 * @property string|null $other_text
 * @property int $is_enabled
 * @property int $block_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at

 * @mixin \Eloquent
 */
class BlockContent extends Model
{
    protected $table = 'ysg_block_contents';

    protected $fillable = [
        'id',
        'title',
        'css_class',
        'other_text',
        'content',
        'options',
        'description',
        'image',
        'is_enabled',
        'block_id',
        'sort',
        'start_date',
        'end_date'
    ];

    const START_DATE_ONLY = 'start_date_only';
    const END_DATE_ONLY   = 'end_date_only';

    protected $attributes = [
        'is_enabled' => true
    ];

    public function getIsEnabledAttribute($value)
    {
        return (bool) $value;
    }

    public function getOptionsAttribute($value)
    {
        return $value ? json_decode($value) : [];
    }

    public function block(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\CustomPage\Block');
    }

    public function ysgBlockContentTranslation($languageId = 1){
        return @$this->hasOne(YsgBlockContentTranslation::class,'ysg_block_content_id','id')->where(['language_id' => $languageId])->first();
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(YsgBlockContentTranslation::class,'ysg_block_content_id','id')->where('language_id', $languageId);
    }

    public function getTranslationTitleAttribute()
    {
        return $this->translation->isEmpty() ? $this->title : $this->translation->first()->title;
    }

    public function getTranslationDescriptionAttribute()
    {
        return $this->translation->isEmpty() ? $this->description : $this->translation->first()->description;
    }

    public function getTranslationContentAttribute()
    {
        return $this->translation->isEmpty() ? $this->content : $this->translation->first()->content;
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
