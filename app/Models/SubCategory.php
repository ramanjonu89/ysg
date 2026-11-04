<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SubCategory
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubCategory extends Model
{
    protected $fillable = ['name','category_id','slug','status','meta_title','meta_description'];

    protected $with = ['translation'];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($subCategory) {
            $subCategory->subSubCategories()->delete();
            $subCategory->subCategoriesTranslation()->delete();
            $subCategory->products()->delete();
        });
    }

    protected static function booted()
    {
        static::updated(function ($subcat) {
            if (env('ELASTICSEARCH_ENABLED', false)) {
                // Update all products referencing this subcat
                $products = Product::where('subcategory_id', $subcat->id)->get();

                foreach ($products as $product) {
                    $product->searchable(); // Update Elasticsearch index
                }
            }
        });
    }

    public $level = 2;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class,'subcategory_id');
    }

    public function subSubCategories()
    {
        return $this->hasMany(SubSubCategory::class);
    }

    public function activeSubSubCategories()
    {
        return $this->hasMany(SubSubCategory::class)->where('status', 1);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(SubSubCategory::class, 'sub_category_id');
    }

    public function subCategoryTranslation($languageId = null){
        return $this->hasOne(SubCategoryTranslation::class)->where(['language_id' => $languageId])->first();
    }

    public function subCategoriesTranslation()
    {
        return $this->hasMany(SubCategoryTranslation::class);
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(SubCategoryTranslation::class)->where('language_id', $languageId);
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
        return $this->hasMany(CustomerProduct::class, 'subcategory_id');
    }

    public function scopeViewableSubCategories()
    {
        return $this->category ==! null && $this->category->status === 1;
    }
    
    public function getSubCategoryLink(): string
    {
        return route('category.view', 
                [
                    'category_slug'    => $this->category->slug, 
                    'subcategory_slug' => $this->slug
                ]);
    }
}
