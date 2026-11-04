<?php

namespace App\Console\Commands;

use App\Models\CouponUsage;
use App\Models\Order;
use App\ObjectModels\Coupon;
use App\Services\CartService;
use Illuminate\Console\Command;

class SyncCouponUsagesDiscountValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:sync_coupon_usages_discount_value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync coupon usages discount value';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $couponUsages = CouponUsage::all();

        foreach ($couponUsages as $coupon) {
            if (!$coupon->coupon) {
                var_dump('coupon is not exits, maybe it is deleted, update discount value to 0');
                $coupon->discount_value = 0;
                $coupon->save();
                continue;
            }
            $order = Order::find($coupon->order_id);
            if (!$order) {
                continue;
            }

            try {
                $cart = CartService::createCartFromOrder($order);
                $couponObject = new Coupon($coupon->coupon);
                
                $coupon->discount_value = $cart->getCouponDiscountValue($couponObject);
                var_dump($coupon->discount_value);
            } catch (\Exception $e) {
                $coupon->discount_value = 0;
                var_dump($e->getMessage());
            }

            $coupon->save();
        }
    }
}
