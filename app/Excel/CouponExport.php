<?php

namespace App\Excel;

use App\Models\Coupon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class CouponExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    protected $index = 0;
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $is_cash_voucher = !empty($this->data['is_cash_voucher']) ? $this->data['is_cash_voucher'] : '';
        $search = !empty($this->data['search']) ? $this->data['search'] : '';

        $type = [];
        if ($is_cash_voucher) {
            array_push($type, Coupon::TYPE_CASH_BASE);
        } else {
            array_push($type, Coupon::TYPE_PRODUCT_BASE, Coupon::TYPE_CART_BASE, Coupon::TYPE_CATEGORY_BASE);
        }

        $query = Coupon::query();
        
        if (!empty($type)) {
            $query->whereIn('type', $type);
        }
        
        if (!empty($search)) {
            $query->where('code', 'like', '%' . $search . '%');
        }
        
        $query->with('shop')->orderBy('id', 'desc');
        
        return $query->get();
    }

    public function headings(): array
    {
        $headers = ['#', 'Code', 'Type', 'Seller/Vendor', 'Usage/Total', 'Start Date', 'End Date', 'Created Date', 'Modified Date'];
        
        if (empty($this->data['is_cash_voucher'])) {
            $headers[] = 'Published';
        }
        $headers[] = 'Active';
        
        return $headers;
    }

    public function map($coupon): array
    {
        $this->index++;
        
        $type_name = '';
        switch ($coupon->type) {
            case 'cart_base':
                $type_name = 'Cart Base';
                break;
            case 'product_base':
                $type_name = 'Product Base';
                break;
            case 'category_base':
                $type_name = 'Category Base';
                break;
            case 'cash_base':
                $type_name = 'Cash Base';
                break;
        }

        $row = [
            $this->index,
            $coupon->code,
            $type_name,
            $coupon->shop ? $coupon->shop->name : '',
            $coupon->number_of_uses . ' / ' . $coupon->getTotalCoupon(),
            date('d-m-Y', $coupon->start_date),
            date('d-m-Y', $coupon->end_date),
            $coupon->created_at->format('d-m-Y H:i:s'),
            $coupon->updated_at->format('d-m-Y H:i:s')
        ];
        
        if (empty($this->data['is_cash_voucher'])) {
            $row[] = $coupon->published === 1 ? 'Published' : 'Not Published';
        }
        $row[] = $coupon->is_active === 1 ? 'Active' : 'Inactive';
        
        return $row;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Determine the number of columns based on headers
                $columnCount = count($this->headings());
                $lastColumn = chr(64 + $columnCount); // Convert to letter (A=65, so 64+1=A)
                
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                $sheet->setCellValue('A1', 'YSG Singapore - Coupon Export');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A2:' . $lastColumn . '2')->getFont()->setBold(true);

                // Auto-size columns
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
}
