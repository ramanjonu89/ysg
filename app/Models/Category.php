<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $banner
 * @property string|null $icon
 * @property int $featured
 * @property int $top
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    public $level = 1;

    protected $fillable = [
        'name',
        'commision_rate',
        'banner',
        'icon',
        'featured',
        'slug',
        'status',
        'meta_title',
        'meta_description',
        'digital',
        'is_age_restricted'
    ];

    protected $with = ['translation'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('alphabetical', function(Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
        static::deleting(function ($category) {
            $category->subCategories()->delete();
            $category->subSubCategoriesManyThrough()->delete();
            $category->homeCateogory()->delete();
            $category->categoriesTranslation()->delete();
            $category->products()->delete();
        });
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function subSubCategories()
    {
        return $this->hasMany(SubSubCategory::class);
    }

    public function activeSubCategories()
    {
        return $this->hasMany(SubCategory::class)->where('status', 1);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function homeCategory()
    {
        return $this->hasMany(HomeCategory::class);
    }


    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function categoryTranslation($languageId = 1){
        return @$this->hasOne(CategoryTranslation::class)->where(['language_id' => $languageId])->first();
    }

    public function categoriesTranslation()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function subSubCategoriesManyThrough(){
        return $this->hasManyThrough(SubSubCategory::class, SubCategory::class);
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(CategoryTranslation::class)->where('language_id', $languageId);
    }

    public function getTranslationNameAttribute()
    {
        return $this->translation->isEmpty() ? $this->name : $this->translation->first()->name;
    }

    public function getTranslationMetaTitleAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_title : $this->translation->first()->meta_title;
    }

    public function getTranslationMetaDescriptionAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_description : $this->translation->first()->meta_description;
    }

    public function classified_products()
    {
        return $this->hasMany(CustomerProduct::class);
    }

    public function getCategoryLink(): string
    {
        return route('category.view', $this->slug);
    }

    public function getProductStocksAttribute()
    {
        return $this->products()->without(['flashDeals','translation'])->sum('current_stock');
    }

    public function getProductSalesAttribute()
    {
        return $this->products()->without(['flashDeals','translation'])->sum('num_of_sale');
    }

    public function reviewCategories()
    {
        return $this->belongsToMany(\App\Models\ReviewCategory::class, 'category_review_categories', 'category_id', 'review_category_id');
    }
}
