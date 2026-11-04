<?php

namespace App\Console\Commands;

use App\Services\OmsService;
use App\Services\OrderService;
use Illuminate\Console\Command;

class OmsSyncUpdate extends Command
{
    
    /**
     * @var OmsService
     */
    protected $omsService;
    /**
     * @var OrderService
     */
    protected $orderService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oms:sync:update {orderCode}';

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
    public function __construct(OmsService $omsService, OrderService $orderService)
    {
        parent::__construct();
        $this->omsService = $omsService;
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

        $status = $this->omsService->syncOrderUpdate($order);

        if ($status) {
            $this->info('order: '.$orderCode.' sync success');
        } else {
            $this->error('order: '.$orderCode.' sync failed');
        }
    }
}
