<?php

namespace App\Excel;

use App\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class OrderExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    protected $index = 0;
    protected $user;
    protected $data;
    protected $mergeRanges = [];

    public function __construct(User $user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $params = [];

        $params['code'] = !empty($this->data['code']) ? $this->data['code'] : null;
        $params['start_date'] = !empty($this->data['start_date']) ? $this->data['start_date'] : null;
        $params['end_date'] = !empty($this->data['end_date']) ? $this->data['end_date'] : null;

        return $this->prepareCollections($params);
    }

    private function prepareCollections($params)
    {
        $query = $this->buildQuery($params)->get();

        $grouped = collect($query)->groupBy('order_number');
        $result = collect();
        $currentRow = 3;

        foreach ($grouped as $orderNumber => $items) {
            $index = 0;
            $startRow = $currentRow;

            foreach ($items as $row) {
                $rowCopy = clone $row;
                if ($index > 0) {
                    $rowCopy->seller_voucher_discount_amount = null;
                    $rowCopy->shipping_cost = null;
                    $rowCopy->grand_total = null;
                }

                $result->push($rowCopy);
                $index++;
                $currentRow++;
            }

            $endRow = $currentRow - 1;

            if ($endRow > $startRow) {
                $this->mergeRanges[] = [
                    'discount_amount' => ['Q' . $startRow, 'Q' . $endRow],
                    'ysg_discount_amount' => ['R' . $startRow, 'R' . $endRow],
                    'shipping_cost'   => ['S' . $startRow, 'S' . $endRow],
                    'grand_total' => ['T' . $startRow, 'T' . $endRow],
                ];
            }
        }

        return $result;
    }

    public function buildQuery($params)
    {
        $query = DB::table('order_details')
            ->select(
                'brands.name as brand',
                'categories.name as category',
                'orders.date as order_date',
                'orders.code as order_number',
                'orders.payment_status as payment_status',
                'split_orders.grand_total as grand_total',
                'split_orders.shipping_cost as shipping_cost',
                'split_orders.delivery_status as fulfilment_status',
                'split_orders.shipping_type as shipping_type',
                'split_orders.discount_amount as discount_amount',
                'split_orders.ysg_discount_amount as ysg_discount_amount',
                'order_details.rsp_price as rsp_price',
                'order_details.discount as discount',
                'order_details.discount_type as discount_type',
                'order_details.discount_type as discount_type',
                'order_details.variation as variation',
                'order_details.price as price',
                'order_details.quantity as quantity',
                DB::raw("CASE
                            WHEN products.id IS NULL THEN order_details.product_name
                            ELSE products.name
                        END as product_name"),
                DB::raw("CASE
                            WHEN (order_details.variation IS NOT NULL AND order_details.variation != '')
                                AND product_stocks.sku IS NOT NULL THEN product_stocks.sku
                            ELSE products.unit
                        END as sku"),
                'shops.name as shop_name',
            )
            ->leftJoin('orders', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('shops', 'products.user_id', '=', 'shops.user_id')
            ->leftJoin('split_orders', 'split_orders.id', '=', 'order_details.split_order_id')
            ->leftJoin('product_stocks', function($join) {
                $join->on('product_stocks.product_id', '=', 'order_details.product_id')
                    ->on('product_stocks.variant', '=', 'order_details.variation');
            });

        $query->where('order_details.seller_id', $this->user->id);

        if (!empty($params['start_date'])) {
            $query->where('orders.date', '>=', $params['start_date']);
        }

        if (!empty($params['end_date'])) {
            $query->where('orders.date', '<=', $params['end_date']);
        }

        if (!empty($params['code'])) {
            $query->where('orders.code', $params['code']);
        }

        $query->orderBy('orders.created_at', 'asc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Order Date',
            'Order Number',
            'Shop Name',
            'Brand',
            'Category',
            'Item Name',
            'Payment Status',
            'Fulfilment Status',
            'Qty',
            'Sku/Unit',
            'RSP',
            'Discount',
            'Discount Type',
            'Unit Selling Price',
            'Sub Total',
            'Seller Voucher Discount Amount',
            'YSG Voucher Discount Amount',
            'Total Shipping Fee',
            'Grand Total',
        ];
    }

    /**
     * @var $entity
     */

    public function map($entity): array
    {
        $this->index++;
        return [
            $this->index,
            $this->formatDate($entity->order_date),
            $entity->order_number,
            $entity->shop_name,
            $entity->brand,
            $entity->category,
            $entity->product_name,
            $entity->payment_status,
            $entity->fulfilment_status,
            $entity->quantity,
            $entity->sku,
            $entity->rsp_price,
            $entity->discount,
            $entity->discount_type,
            $this->formatPrice($entity->price / $entity->quantity),
            $entity->price,
            $entity->discount_amount - $entity->ysg_discount_amount,
            $entity->ysg_discount_amount,
            $entity->shipping_cost,
            $entity->grand_total,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:T1');
                $sheet->setCellValue('A1', 'YSG Singapore');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A2:T2')->getFont()->setBold(true);

                foreach ($sheet->getColumnIterator() as $column) {
                    foreach ($column->getCellIterator() as $cell) {
                        if ($cell->getValue() === null) {
                            $cell->setValue(0);
                        }
                    }
                }

                foreach ($this->mergeRanges as $range) {
                    $sheet->mergeCells($range['discount_amount'][0] . ':' . $range['discount_amount'][1]);
                    $sheet->mergeCells($range['ysg_discount_amount'][0] . ':' . $range['ysg_discount_amount'][1]);
                    $sheet->mergeCells($range['shipping_cost'][0] . ':' . $range['shipping_cost'][1]);
                    $sheet->mergeCells($range['grand_total'][0] . ':' . $range['grand_total'][1]);
                }
            },
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    private function formatDate($date) {
        return date('Y-m-d H:i:s', $date);
    }

    private function formatPrice($price) {
        return number_format($price, config('business_setting.no_of_decimals'));
    }
}
