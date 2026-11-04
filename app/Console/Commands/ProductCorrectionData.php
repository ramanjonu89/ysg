<?php

namespace App\Console\Commands;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Excel\ProductImport\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCorrectionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // using like: " php artisan product:correction-data 1"
    // or like: " php artisan product:correction-data"
    protected $signature = 'product:correction-data {id?}';

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

        $id = $this->argument('id');

        $this->fixColors($id);

        $this->fixVariantProduct($id);

        $this->info('END COMMAND');
    }

    private function fixVariantProduct($id = null) {
        $products = $this->getProductWithWrongVariantProduct($id);
        foreach ($products as $product) {
            if ($product->choice_options != '[]' || $product->colors != '[]') {
                $this->updateVariantProduct(
                    [
                        'id' => $product->id,
                        'variant_product' => 1,
                    ]
                );
                $this->info("Product $product->id updated variant_product");
            }
        }
    }

    private function updateVariantProduct ($data) {
        return DB::update("
            UPDATE 
                products
            SET
                variant_product = :variant_product,
                updated_at = :updated_at
            WHERE
                id = :id
            ",
            [
                ":id" => $data['id'],
                ":variant_product" => $data['variant_product'],
                ":updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }

    private function getProductWithWrongVariantProduct ($id) {
        $query = Product::query()
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('choice_options', '!=', '[]')
                        ->orWhere('colors', '!=', '[]');
                })
                    ->where('variant_product', '=', 0);
            });
        if ($id) {
            $query->orWhere('id' , '=', $id);
        }
        return $query->get();
    }

    private function fixColors($id = null) {
        $products = $this->getProductWithOutColors($id);
        foreach ($products as $product) {
            if (is_null($product->colors) || $product->colors == '') {
                $this->updateProductColors(
                    [
                        'id' => $product->id,
                        'colors' => json_encode([]),
                    ]
                );
                $this->info("Product $product->id updated colors");
            }
        }
    }

    private function updateProductColors ($data) {
        return DB::update("
            UPDATE 
                products
            SET
                colors = :colors,
                updated_at = :updated_at
            WHERE
                id = :id
            ",
            [
                ":id" => $data['id'],
                ":colors" => $data['colors'],
                ":updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }

    private function getProductWithOutColors ($id) {
        $query = Product::query()
            ->where(function ($q) {
                $q->whereNull('colors')
                    ->orWhere('colors', '=', '');
            });
        if ($id) {
            $query->orWhere('id' , '=', $id);
        }
        return $query->get();
    }
}
