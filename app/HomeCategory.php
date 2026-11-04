<?php
namespace App;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class HomeCategory extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

