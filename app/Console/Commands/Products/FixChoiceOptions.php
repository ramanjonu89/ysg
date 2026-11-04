<?php

namespace App\Console\Commands\Products;

use App\Models\Product;
use App\Services\AttributeService;
use App\Services\ProductService;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixChoiceOptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // using like: " php artisan user:changePass 1 abc"
    protected $signature = 'products:fix-choice-options {productId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $productService;
    private $attributeService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductService $productService, AttributeService $attributeService)
    {
        $this->productService = $productService;
        $this->attributeService = $attributeService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::find(1536);
        auth()->login($user, true);

        $productId = $this->argument('productId');
        if ($productId) {
            $product = $this->productService->get($productId);
            $this->fixVariantData($product);
        } else {
            $products = Product::query()
                ->where('variant_product', '1')->get();
            foreach ($products as $product) {
                if (in_array($product->id, [17038])) {
                    continue;
                }
                $this->fixVariantData($product);
            }
        }

        print ('ok');

        return 0;
    }

    private function fixVariantData($product)
    {
        try {
            $stocks = $product->stocks;
            if ($stocks && $stocks->count() > 0) {
                $this->updateChoiceOptions($product);
                $this->updateStocks($product, $stocks);
            } else {
                $product->variant_product = 0;
                $product->save();
            }
        } catch (\Exception $e) {
            dump($product->id);
            dump($e->getMessage());
        }
    }

    private function updateChoiceOptions($product)
    {
        // get choice options
        $choiceOptions = $product->choice_options;
        // update choice options
        if ($choiceOptions) {
            $attributes = json_decode($choiceOptions, true);
            foreach ($attributes as $key => $attribute) {
                $attr = $this->attributeService->get($attribute['attribute_id']);
                $attribute['attribute'] = [
                    'value' => $attr->id,
                    'text' => $attr->name,
                ];
                $attributes[$key] = $attribute;
            }

            $attributes = json_encode($attributes);
            $product->choice_options = $attributes;
            $product->save();
        }
    }

    private function updateStocks($product, $stocks)
    {
        $attributes = json_decode($product->choice_options, true);
        if (count($attributes) == 1) {
            $variants = $attributes[0]['values'];
            foreach ($variants as $variant) {
                $str = $variant;
                // trim all space from string
                $str = preg_replace('/\s+/', '', $str);
                if (isset($stocks[$str])) {
                    $stock = $stocks[$str];
                    $stock->variant = $variant;
                    $stock->save();
                }
            }
        }
    }
}
