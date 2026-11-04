<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CustomPage\Block;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Microsite extends Model
{
    protected $guarded = ['id'];

    const IS_ENABLED = '1';

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'microsite_id')->where('is_enabled', '=', self::IS_ENABLED)->orderBy('sort');
    }

    public function allBlocks(): HasMany
    {
        return $this->hasMany(Block::class, 'microsite_id')->orderBy('sort');
    }

    public function micrositeUsers(): HasMany
    {
        return $this->hasMany(MicrositeUser::class, 'microsite_id');
    }

    public function getCustomersAttribute()
    {
        return $this->micrositeUsers->map(function ($item) {
            return $item->microsite;
        });
    }
}