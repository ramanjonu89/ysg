<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Oms\Product as SyncService;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Models\Product;

class SyncSellerProducts extends Command
{
    protected $signature = 'oms:sync:seller-products {sellerId}';

    protected $description = 'Sync all active products of a seller to OMS';

    protected $syncService;
    protected $productRepo;
    protected $userRepo;

    public function __construct(SyncService $syncService, ProductRepository $productRepo, UserRepository $userRepo)
    {
        $this->syncService = $syncService;
        $this->productRepo = $productRepo;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    public function handle()
    {
        $sellerId = $this->argument('sellerId');

        try {
            $seller = $this->userRepo->find($sellerId);
            if (!$seller) {
                $this->error('Seller not found with ID: ' . $sellerId);
                return;
            }

            $this->info('Found seller: ' . $seller->name);

            $activeProducts = Product::where('user_id', $sellerId)
                ->where('published', 1)
                ->pluck('id')
                ->toArray();

            if (empty($activeProducts)) {
                $this->warn('No active products found for seller ID: ' . $sellerId);
                return;
            }

            $this->info('Found ' . count($activeProducts) . ' active products to sync');

            $syncResult = $this->syncService->runSync($sellerId, $activeProducts);
            
            if ($syncResult['status'] === true) {
                $this->info('Sync completed successfully for ' . count($activeProducts) . ' products');
                if (!empty($syncResult['responseData'])) {
                    $this->info('Response: ' . json_encode($syncResult['responseData']));
                }
            } else {
                $this->error('Sync failed: ' . $syncResult['message']);
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
