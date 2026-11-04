<?php
namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FulfilmentReport implements WithHeadings, WithMapping, FromCollection
{
    private $orderDetails;
    public function __construct($orderDetails)
    {
        $this->orderDetails = $orderDetails;

        return $this;
    }

    public function collection()
    {
        return $this->orderDetails;
    }

    //Mapping to each column name of headings
    public function map($orderDetail): array
    {
        $order = \App\Models\Order::where('id', $orderDetail->order_id)->first();
        $customerInfo = json_decode($order->shipping_address);
        $product = \App\Models\Product::where('id', $orderDetail->product_id)->first();
        $sku = $product === null ? 'deleted' : $product->unit;
        $datecreated = Carbon::createFromFormat("Y-m-d H:i:s", $order->created_at)->toDateTimeString();
        // var_dump($datecreated);

        return [
            $order->id,
            $datecreated,
            $customerInfo->name,
            $customerInfo->address,
            $customerInfo->email,
            $customerInfo->phone,
            $orderDetail->product_name,
            $sku,
            $orderDetail->quantity
        ];
    }


    //'Order Date', 'Order Number', 'Brand', 'Item Name', 'Payment Status', 'Qty', 'SKU/Unit', 'RSP', 'Discount', 'Discount Type', 'Unit Selling Price', 'Subtotal',
    //set excel headings name
    public function headings(): array
    {
        return [
            'Order ID',
            'Created At',
            'Customer Name',
            'Address',
            'Email',
            'Contact',
            'Product Name',
            'SKU',
            'Quantity'
        ];
    }
}
