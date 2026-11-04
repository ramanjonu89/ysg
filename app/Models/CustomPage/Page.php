<?php
namespace App\Models\CustomPage;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomPage\Page
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $image
 * @property string $description
 * @property string $content
 * @property string $options
 * @property string $type
 * @property boolean $is_enabled
 * @property boolean $need_login
 * @property boolean $exclusive_access
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property int $parent_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at

 * @mixin \Eloquent
 */
class Page extends Model
{
    const TYPE_HOMEPAGE = 'homepage';
    const TYPE_PAGE = 'page';
    const IS_ENABLED = '1';
    const TYPE_SHOP = 'shop';

    protected $table = 'ysg_pages';

    protected $fillable = [
        'id',
        'slug',
        'type',
        'title',
        'image',
        'content',
        'options',
        'description',
        'parent_id',
        'is_enabled',
        'need_login',
        'exclusive_access',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $attributes = [
        'is_enabled' => true,
        'type' => self::TYPE_PAGE
    ];

    public function getOptionsAttribute($value)
    {
        return $value ? json_decode($value) : [];
    }

    public function getAllowDomainsAttribute($value)
    {
        return $value ? json_decode($value) : [];
    }

    public function allBlocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Block::class, 'ysg_page_id')->orderBy('sort');
    }

        public function blocks()
    {
        return $this->allBlocks()->where('is_enabled', '=', self::IS_ENABLED);
    }

    public function scopeActive($query)
    {
        return $query->where('is_enabled', '=', true);
    }

    public function scopeNeedLogin($query)
    {
        return $query->where('need_login', '=', true);
    }

    public function parent() {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(){
        return $this->hasMany(self::class, 'parent_id');
    }

    public function ysgPageTranslation($languageId = 1){
        return @$this->hasOne(YsgPageTranslation::class,'ysg_page_id','id')->where(['language_id' => $languageId])->first();
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(YsgPageTranslation::class,'ysg_page_id','id')->where('language_id', $languageId);
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
    public function getTranslationMetaTitleAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_title : $this->translation->first()->meta_title;
    }

    public function getTranslationMetaDescriptionAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_description : $this->translation->first()->meta_description;
    }

    public function getTranslationMetaKeywordAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_keyword : $this->translation->first()->meta_keyword;
    }
}
