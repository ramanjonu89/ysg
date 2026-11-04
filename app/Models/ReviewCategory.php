<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewCategory extends Model
{
    protected $fillable = ['title', 'enabled'];
    
    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(\App\Models\Category::class, 'category_review_categories', 'review_category_id', 'category_id');
    }

    public function reviews()
    {
        return $this->belongsToMany(\App\Review::class, 'review_review_categories', 'review_category_id', 'review_id');
    }
}

