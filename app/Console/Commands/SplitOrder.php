<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderService;

class SplitOrder extends Command
{
    
    /**
     * @var OrderService
     *
     */
    protected $orderService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:split-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Split order to sub-orders';

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
     * @return mixed
     */
    public function handle()
    {
        $this->orderService->runSplitOrders();
    }
}
