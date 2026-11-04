<?php

namespace App\Console\Commands;

use App\Services\OrderDetailService;
use Illuminate\Console\Command;

class UpdateProductNameForOrderDetail extends Command
{
    
    /**
     * @var OrderDetailService
     *
     */
    protected $orderDetailService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:update-product-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product name for each order detail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OrderDetailService $orderdetailService)
    {
        parent::__construct();
        $this->orderDetailService = $orderdetailService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->orderDetailService->updateProductName();
    }
}
