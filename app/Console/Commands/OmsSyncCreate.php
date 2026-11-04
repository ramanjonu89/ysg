<?php

namespace App\Console\Commands;

use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Console\Command;

class OmsSyncCreate extends Command
{
    
    /**
     * @var CheckoutService
     */
    protected $checkoutService;
    /**
     * @var OrderService
     */
    protected $orderService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oms:sync:create {orderCode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncing process create order from YSG to OMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CheckoutService $checkoutService, OrderService $orderService)
    {
        parent::__construct();
        $this->checkoutService = $checkoutService;
        $this->orderService = $orderService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orderCode = $this->argument('orderCode');
        $order = $this->orderService->getByCode($orderCode);

        $status = $this->checkoutService->syncOmsOrder($order);

        if ($status) {
            $this->info('order: '.$orderCode.' sync success');
        } else {
            $this->error('order: '.$orderCode.' sync failed');
        }
    }
}
