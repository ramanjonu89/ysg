<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Shop;
use App\Services\CustomPage\BlockContentService;
use App\Services\CustomPage\BlockService;
use App\Services\ShopService;
use Illuminate\Console\Command;

class MigrateSellerSetting extends Command
{
    protected $shopService;
    protected $blockService;
    protected $blockContentService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seller-center:migrate-setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ShopService $shopService,
        BlockService $blockService,
        BlockContentService $blockContentService
    )
    {
        $this->shopService = $shopService;
        $this->blockService = $blockService;
        $this->blockContentService = $blockContentService;
        parent::__construct();
    }

    private function migrateFeaturedProductSetting(Shop $shop) {
        try {
            $userId = $shop->user_id;
            $query = Product::query();

            $query->select(
                'products.id',
                'products.user_id',
                'products.featured'
            );
            $query->whereIn('products.user_id', [$userId]);
            $query->where('products.featured', 1);
            $products = $query->get();

            $ids = [];
            foreach ($products as $product) {
                $ids[] = $product->id;
            }

            if ($ids) {
                $data = [
                    'featured_products' => implode(',', $ids)
                ];

                $this->shopService->update($shop->id, $data);
            }
        } catch (\Exception $e) {
        }

    }

    private function migrateSliderSetting(Shop $shop) {
        try {
            $images = json_decode($shop->sliders);
            if ($images) {
                $block = $this->addBlock($shop);
                $count = 1;
                foreach ($images as $image) {
                    $this->addContent($count, $image, $block);
                    $count++;
                }
            }
        } catch (\Exception $e) {
        }
    }

    private function addBlock($shop) {
        $data = [
            'title' => $shop->name,
            'type' => 'microsite_slider',
            'is_enabled' => 1,
            'blockable_id' => $shop->id,
            'page_type' => 'shop',
            'entity_id' => $shop->id
        ];
        return $this->blockService->create($data);
    }

    private function addContent($count, $image, $block) {
        $data = [
            'title' => "image $count",
            'options' => '{"link":null}',
            'image' => '/'.$image,
            'sort' => $count,
            'is_enabled' => 1,
            'block_id' => $block->id
        ];

        $this->blockContentService->create($data);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $query = Shop::query();
        $shops = $query->get();

        foreach ($shops as $shop) {
            $this->migrateFeaturedProductSetting($shop);
            $this->migrateSliderSetting($shop);

            $this->info('shop: '.$shop->name.' success migrate');
        }
    }
}
