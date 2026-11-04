<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductStock;
use App\User;
use Illuminate\Console\Command;
use App\Excel\ProductImport\Service;
use Illuminate\Support\Facades\DB;

class UpdateVariation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // using like: " php artisan user:changePass 1 abc"
    protected $signature = 'update-variation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $sevice;
    protected $cache;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        Service $service
    )
    {
        $this->sevice = $service;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('START COMMAND');

        foreach ($this->getVariationGroups() as $productId => $productStocks) {
            $validOptions = $this->getAllValidOptions($productId);
            if ($validOptions) {
                try {
                    DB::beginTransaction();
                    foreach ($productStocks as $productStock) {
                        $newVariantStr = $this->calculateNewVariantStr($productStock, $validOptions);
                        if ($newVariantStr) {
                            $status = $this->updateProductStock($productStock, $newVariantStr);
                            if (!$status) {
                                throw new \Exception('Error: '.$productId.' '.$productStock->id);
                            }
                        }
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error($e->getMessage());
                }
            }
        }

        $this->info('END COMMAND');
    }

    private function calculateNewVariantStr($productStock, $validOptions): string
    {
        $variant = $productStock->variant;
        $currentVariants = [];
        foreach ($validOptions as $option) {
            $pos = strpos($variant, $option);
            if ($pos !== false) {
                $variant = str_replace($option, '', $variant);
                $currentVariants[$pos] = $option;
            }
        }
        ksort($currentVariants);

        if ($currentVariants) {
            return implode(ProductStock::SPLIT_SYMBOL, $currentVariants);
        }
        return '';
    }

    private function getAllValidOptions($productId): array
    {
        try {
            $product = Product::find($productId);
            $choiceOptions = json_decode($product->choice_options, true);

            return collect($choiceOptions)->pluck('values')->flatten(1)
                ->sortByDesc(function ($option) {
                    return strlen($option);
                })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getVariationGroups () {
        return ProductStock::query()
            ->where('variant', 'like', '%-%')
            ->get()
            ->groupBy('product_id');
    }

    private function updateProductStock(ProductStock $productStock, $variant): bool
    {
        $productStock->variant = $variant;
        return $productStock->save();
    }


}
