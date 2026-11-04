<?php
namespace App\Models\CustomPage;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomPage\PageBlock
 *
 * @property int $id
 * @property int $sort
 * @property int $page_id
 * @property int $block_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at

 * @mixin \Eloquent
 */
class PageBlock extends Model
{
    protected $table = 'ysg_page_blocks';

    protected $fillable = [
        'id',
        'sort',
        'page_id',
        'block_id',
    ];

    public function block()
    {
        return $this->hasOne('App\Models\CustomPage\Block', 'id', 'block_id');
    }

    public function page()
    {
        return $this->hasOne('App\Models\CustomPage\page', 'id', 'page_id');
    }
}
