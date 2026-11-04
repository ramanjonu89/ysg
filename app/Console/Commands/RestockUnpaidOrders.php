<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RestockUnpaidOrders extends Command
{
    protected $signature = 'orders:restock-unpaid';
    protected $description = 'Restock products from unpaid orders that are between 10 to 75 minutes old';

    public function handle()
    {
        $this->info('Starting restock process for unpaid orders...');

        $startTime = Carbon::now()->subMinutes(75);
        $endTime = Carbon::now()->subMinutes(10);
        
        $unpaidOrders = Order::where('payment_status', Order::PAYMENT_STATUS_UNPAID)
            ->where('restock_status', 0)
            ->where('created_at', '>=', $startTime)
            ->where('created_at', '<=', $endTime)
            ->get();

        $this->info("Found {$unpaidOrders->count()} unpaid orders to restock");

        $restockedCount = 0;
        
        $orderService = app()->make(OrderService::class);
        
        foreach ($unpaidOrders as $order) {
            try {
                $this->info("Processing order #{$order->code}");
                
                $result = $orderService->restockOrder($order);
                
                $restockedCount++;
                $this->info("Successfully restocked and cancelled order #{$order->code}");
                $this->info("- Order details cancelled: {$result['order_details_count']}");
                $this->info("- Split orders cancelled: {$result['split_orders_count']}");
                
            } catch (\Exception $e) {
                $this->error("Failed to restock order #{$order->code}: " . $e->getMessage());
            }
        }

        $this->info("Restock process completed. Restocked {$restockedCount} orders.");
        
        return 0;
    }
}
