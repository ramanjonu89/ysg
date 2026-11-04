<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class UpdateYsgDiscountAmount extends Command
{
    protected $orderService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:update-ysg-discount-amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update ysg discount amount in split orders table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('START COMMAND');

        $this->orderService->runUpdateYsgDiscountAmountInSplitOrders();
        $this->info('Updated Split Orders Successfully');

        $this->info('END COMMAND');
    }
}
