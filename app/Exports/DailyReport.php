<?php
namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyReport implements WithHeadings, WithMapping, FromCollection
{
    public function __construct(int $userid)
    {
        $this->userid = $userid;

        return $this;
    }

    public function collection()
    {
        //$todaydate = "2020-12-11";
        $order = \App\OrderDetail::where('payment_status', 'paid')->whereDate('created_at', Carbon::yesterday())->where('seller_id', $this->userid)->get();

        //$order = \App\OrderDetail::where('payment_status','paid')->whereDate('created_at',$todaydate)->Where('seller_id',$this->userid)->get();
        return $order;
    }

    //Mapping to each column name of headings
    public function map($order): array
    {
        // var_dump($order);
        $orderx = \App\Models\Order::where('id', $order->order_id)->first();
        $datecreated = date("Y-m-d H:i:s", substr($orderx->date, 0, 10));
        $brand_id = \App\Brand::where('id', $order->product->brand_id)->first();
        $sellingPrice = $this->calcDiscount($order->rsp_price, $order->discount_type, $order->discount);

        // Start calculate discount coupon, @todo: need update later
        $coupon = 'N/A';
        $couponDiscount = 0;
        $couponDiscountType = 'N/A';
        $total = $order->price;

        if ($c = $orderx->coupons()->first()) {
            $seller = \App\Shop::where('user_id', $order->seller_id)->first();
            if ($c->seller_id == $seller->id && $c->discount_type == 'percent') {
                $coupon = $c->code;
                $couponDiscount = $c->discount;
                $couponDiscountType = $this->renderDiscountType($c->discount_type);
                $total = $this->calcDiscount($order->price, $c->discount_type, $c->discount);
            }
        }

        return [
            $datecreated,
            $orderx->code,
            $brand_id ? $brand_id->name : '',
            $order->product->name,
            $order->payment_status,
            $order->quantity,
            $order->product->unit,
            $order->rsp_price,
            $order->discount,
            $this->renderDiscountType($order->discount_type),
            $this->formatPrice($sellingPrice),
            $this->formatPrice($order->price),
            $couponDiscount,
            $couponDiscountType,
            $this->formatPrice($total),
        ];
    }


    //'Order Date', 'Order Number', 'Brand', 'Item Name', 'Payment Status', 'Qty', 'SKU/Unit', 'RSP', 'Discount', 'Discount Type', 'Unit Selling Price', 'Subtotal',
    //set excel headings name
    public function headings(): array
    {
        return [
            'Order Date',
            'Order Number',
            'Brand',
            'Item Name',
            'Payment Status',
            'Qty',
            'SKU/Unit',
            'RSP',
            'Discount',
            'Discount Type',
            'Unit Selling Price',
            'Subtotal',
            'Voucher Discount Amount',
            'Voucher Discount Type',
            'Total'
        ];
    }

    public function calcDiscount($price, $discountType, $discountValue)
    {
        if ($discountType == "percent") {
            $p = $price - ($price * $discountValue / 100);
        } else {
            $p = $price - $discountValue;
        }

        return $p;
    }

    public function formatPrice($price)
    {
        return number_format((float)$price, 2, '.', '');
    }

    public function renderDiscountType($type) {
        return $type == 'percent' ? 'Percent' : 'Dollar Value';
    }
}
