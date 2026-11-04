<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Brand
 *
 * @property int $id
 * @property string $name
 * @property string|null $logo
 * @property int $top
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Brand extends Model
{
    const STATUS_ACTIVE = 1; 
    const STATUS_INACTIVE = 0;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('alphabetical', function(Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
    }

    protected static function booted()
    {
        static::updated(function ($brand) {
            if (env('ELASTICSEARCH_ENABLED', false)) {
                // Update all products referencing this brand
                $products = Product::where('brand_id', $brand->id)->get();

                foreach ($products as $product) {
                    $product->searchable(); // Update Elasticsearch index
                }
            }
        });
    }
}
