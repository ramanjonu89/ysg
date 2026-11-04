<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Oms\Product as SyncService;
use App\Repositories\ProductRepository;

class OmsSyncProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // using like: "php artisan oms:sync:product 123 456"
    protected $signature = 'oms:sync:product {userId} {productId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command use to sync product from YSG to OMS';

    /**
     * @property SyncService
     */
    protected $syncService;

    /**
     * @property ProductRepository
     */
    protected $productRepo;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SyncService $syncService, ProductRepository $productRepo)
    {
        $this->syncService = $syncService;
        $this->productRepo = $productRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $productId = $this->argument('productId');

        try {
            $this->productRepo->find($productId);
        } catch (\Exception $e) {
            $this->error('Can not find product with id:'.$productId);
            return;
        }

        $syncResult = $this->syncService->runSync($userId, [$productId]);
        if ($syncResult['status'] === true) {
            $this->info('Sync success');
        } else {
            $this->error($syncResult['message']);
        }

        return;
    }
}
