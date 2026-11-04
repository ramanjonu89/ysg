<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductStock;
use App\User;
use Illuminate\Console\Command;
use App\Excel\ProductImport\Service;
use Illuminate\Support\Facades\DB;

class UpdateProductsMinMaxPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update-min-max-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product min max price from product stocks and discount value';

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

        $this->updateStocks();
        $this->updateProducts();

        $this->info('END COMMAND');
    }

    private function updateStocks() {
        $status = \App\Models\ProductStock::chunk(200, function ($stocks) {
            DB::transaction(function () use ($stocks) {
                foreach ($stocks as $stock) {
                    DB::table('product_stocks')
                        ->where('id', $stock->id)
                        ->update([
                            'final_price' => $stock->price - $stock->discount_amount,
                        ]);
                }

            });
        });

        $this->info('Product Stocks updated');
    }

    private function updateProducts() {
        $status = \App\Models\Product::chunk(200, function ($products) {
            DB::transaction(function () use ($products) {
                foreach ($products as $product) {
                    $stocks = $product->stocks;
                    if ($stocks->count() !== 0) {
                        $minPrice = $stocks->min('price');
                        $minFinalPrice = $stocks->min('final_price');
                        $maxPrice = $stocks->max('price');
                        $maxFinalPrice = $stocks->max('final_price');
                    } else {
                        $minPrice = $maxPrice = $product->unit_price;
                        $minFinalPrice = $maxFinalPrice = $product->final_price;
                    }

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'min_price' => $minPrice,
                            'min_final_price' => $minFinalPrice,
                            'max_price' => $maxPrice,
                            'max_final_price' => $maxFinalPrice,
                        ]);
                }
            });
        });

        $this->info('Products updated');
    }
}
