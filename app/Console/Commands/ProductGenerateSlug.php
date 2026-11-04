<?php

namespace App\Console\Commands;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Excel\ProductImport\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductGenerateSlug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // using like: " php artisan product:generate-slug 1"
    // or like: " php artisan product:generate-slug"
    protected $signature = 'product:generate-slug {id?}';

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
        $products = $this->getProductsWithoutSlug($id);
        foreach ($products as $product) {

            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $product->name)) . '-' . Str::random(5);
            $slug = strtolower($slug);

            $this->updateProduct(
                [
                    'id' => $product->id,
                    'slug' => $slug,
                ]
            );

            $this->info("Product $product->id updated with slug: $slug");
        }

        $this->info('END COMMAND');
    }

    private function updateProduct($data) {
        $status = DB::update("
            UPDATE 
                products
            SET
                slug = :slug,
                updated_at = :updated_at
            WHERE
                id = :id
            ",
            [
                ":id" => $data['id'],
                ":slug" => $data['slug'],
                ":updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ]);

        return $status;
    }

    private function getProductsWithoutSlug ($id = null) {
        $query = Product::query()
            ->where(function ($q) {
                $q->where('slug', 'is null')
                    ->orWhere('slug', '=', '');
            });

        if ($id) {
            $query->orWhere('id' , '=', $id);
        }
        return $query->get();
    }

}
