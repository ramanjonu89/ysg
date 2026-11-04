<?php

namespace App\Console\Commands;

use App\Notifications\OrderStatusChanged;
use Illuminate\Console\Command;
use App\Services\OrderService;

class ResendEmailOrders extends Command
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
    protected $signature = 'ysg:resend-email-orders {orderCode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-send email to orders';

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
        $this->info('START COMMAND');
        $ordernumber = $this->argument('orderCode');

        $order = $this->orderService->getByCode($ordernumber);

        if ($order) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $statusDelivery = $orderDetail->delivery_status;
                $this->info('status: ' . $orderDetail->delivery_status);
                $order->user->notify(new OrderStatusChanged($orderDetail, $statusDelivery, $orderDetail->shipping_type));
            }
        }
        $this->info('END COMMAND');
    }
}
