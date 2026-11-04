<?php
namespace App\Models\CustomPage;

use App\Models\Microsite;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use stdClass;

/**
 * App\Models\CustomPage\Block
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string|null $image
 * @property string $type
 * @property string|null $options
 * @property int $sort
 * @property boolean $is_enabled
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at

 * @mixin \Eloquent
 */
class Block extends Model
{
    const TYPE_IMAGE_LINK = 'image_link';
    const TYPE_BANNER = 'banner';
    const TYPE_SHOP_LIST = 'shop_list';
    const TYPE_CA_SHOP_LIST = 'ca_shop_list';
    const TYPE_CA_BRAND_LIST = 'ca_brand_list';
    const TYPE_TEXT = 'text';
    const TYPE_VIDEO = 'video';

    const TYPE_PRODUCT_CAROUSEL = 'product_carousel';
    const TYPE_PRODUCT_CAROUSEL_LIMIT_CONTENT = 15;

    const TYPE_MICROSITE_SLIDER = 'microsite_slider';
    const TYPE_MICROSITE_ONE_IMAGE = 'microsite_one_image';
    const TYPE_MICROSITE_TWO_IMAGE = 'microsite_two_images';
    const TYPE_MICROSITE_THREE_IMAGE = 'microsite_three_images';
    const TYPE_MICROSITE_FOUR_IMAGE = 'microsite_four_images';

    const NEED_CONTENT_TYPES = [
        self::TYPE_PRODUCT_CAROUSEL,
        self::TYPE_PRODUCT_CAROUSEL_LIMIT_CONTENT,
        self::TYPE_MICROSITE_SLIDER,
        self::TYPE_MICROSITE_ONE_IMAGE,
        self::TYPE_MICROSITE_TWO_IMAGE,
        self::TYPE_MICROSITE_THREE_IMAGE,
        self::TYPE_MICROSITE_FOUR_IMAGE,
        self::TYPE_SHOP_LIST,
        self::TYPE_CA_SHOP_LIST,
        self::TYPE_CA_BRAND_LIST,
        self::TYPE_IMAGE_LINK,
    ];

    protected $table = 'ysg_blocks';

    protected $guarded = ['id'];

    protected $attributes = [
        'is_enabled' => true,
        'type' => self::TYPE_IMAGE_LINK
    ];

    const IS_ENABLED = '1';
    const START_DATE_ONLY = 'start_date_only';
    const END_DATE_ONLY   = 'end_date_only';
    const DATE_RANGE      = 'date_range';

    public function getIsEnabledAttribute($value)
    {
        return (bool) $value;
    }

    public function getOptionsAttribute($value)
    {
        return $value ? json_decode($value) : new stdClass();
    }

    public function pages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany('App\Models\CustomPage\Page', 'ysg_page_blocks');
    }

    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function microsite(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Microsite::class);
    }

    public function blockContents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->allBlockContents()->where('is_enabled', '=', self::IS_ENABLED);
    }

    public function getProducts(): array
    {
        $result = [];
        if ($this->type == self::TYPE_PRODUCT_CAROUSEL) {
            $items = $this->blockContents;
            foreach ($items as $item) {
                $data = $item->options;
                if ($data) {
                    $productId = $data->id;
                    $product = Product::query()
                        ->where('id', $productId)->published()->first();
                    if ($product) {
                        $result[] = $product;
                    }
                }
            }
        }
        return $result;
    }

    public function allBlockContents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BlockContent::class)->orderBy('sort');
    }

    /**
     * Get the owning blockable model.
     */
    public function blockable()
    {
        return $this->morphTo();
    }

    public function ysgBlockTranslation($languageId = 1){
        return @$this->hasOne(YsgBlockTranslation::class,'ysg_block_id','id')->where(['language_id' => $languageId])->first();
    }

    public function translation($languageId = null)
    {
        if (is_null($languageId)) {
            $languageId = session('sessionLanguageId');
        }
        return $this->hasMany(YsgBlockTranslation::class,'ysg_block_id','id')->where('language_id', $languageId);
    }

    public function translations()
    {
        return $this->hasMany(YsgBlockTranslation::class,'ysg_block_id','id');
    }

    public function getTranslationTitleAttribute()
    {
        return $this->translation->isEmpty() ? $this->title : $this->translation->first()->title;
    }

    public function getTitle($languageId = null)
    {
        $translation = $this->translation($languageId)->first();
        return $translation ? $translation->title : $this->title;
    }

    public function getTranslationDescriptionAttribute()
    {
        return $this->translation->isEmpty() ? $this->description : $this->translation->first()->description;
    }

    public function getTranslationContentAttribute()
    {
        return $this->translation->isEmpty() ? $this->content : $this->translation->first()->content;
    }

    public function needsContent()
    {
        return in_array($this->type, self::NEED_CONTENT_TYPES);
    }

    public function isExpired() {
        $now = strtotime(date('d-m-Y H:i:s'));

        if (empty($this->start_date) && empty($this->end_date)) {
            return false;
        } 

        if (!empty($this->start_date) && !empty($this->end_date)) {
            return  $now < $this->start_date || 
                    $now > $this->end_date;

        } elseif (!empty($this->start_date)) {
            return $now <= $this->start_date;

        } elseif (!empty($this->end_date)) {
            return $now >= $this->end_date;
        }

        return false;
    }
}
