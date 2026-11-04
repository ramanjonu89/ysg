<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SubSubCategory
 *
 * @property int $id
 * @property int $sub_category_id
 * @property string $name
 * @property string $brands
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory whereBrands($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory whereSubCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubSubCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubSubCategory extends Model
{
    public $level = 3;
    protected $fillable = ['name','sub_category_id','slug','status','meta_title','meta_description'];
    protected $with = ['translation'];

    const STATUS_ACTIVE = 1; 
    const STATUS_INACTIVE = 0;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('alphabetical', function(Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
        static::deleting(function ($subSubCategory) {
            $subSubCategory->subSubCategoriesTranslation()->delete();
            $subSubCategory->products()->delete();
        });
    }

    protected static function booted()
    {
        static::updated(function ($subsubcat) {
            if (env('ELASTICSEARCH_ENABLED', false)) {
                // Update all products referencing this subsubcat
                $products = Product::where('subsubcategory_id', $subsubcat->id)->get();

                foreach ($products as $product) {
                    $product->searchable(); // Update Elasticsearch index
                }
            }
        });
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function subSubCategoryTranslation($languageId = null){
        return $this->hasOne(SubSubCategoryTranslation::class)->where(['language_id' => $languageId])->first();
    }

    public function products()
    {
        return $this->hasMany(Product::class,'subsubcategory_id');
    }

    public function subSubCategoriesTranslation()
    {
        return $this->hasMany(SubSubCategoryTranslation::class);
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(SubSubCategoryTranslation::class)->where('language_id', $languageId);
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
        return $this->hasMany(CustomerProduct::class, 'subsubcategory_id');
    }
    
    public function scopeViewableSubSubCategories()
    {
        return $this->subcategory !== null && $this->subcategory->category !== null
               && $this->subcategory->status === 1 && $this->subcategory->category->status === 1;

    }

    public function getSubSubCategoryLink(): string
    {
        return route('category.view', 
                [
                    'category_slug'       => $this->subCategory->category->slug,
                    'subcategory_slug'    => $this->subCategory->slug,
                    'subsubcategory_slug' => $this->slug
                ]);
    }
}