<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 22/05/2022
 * Time: 17:12
 */

namespace App\Services\ProductExport;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class Export
 * @package App\Services\ProductExport
 * @property
 */
class Export implements FromArray, WithHeadings
{
    protected $products;

    public function __construct(array $products)
    {
        $this->products = $products;
    }

    public function headings(): array
    {
        return [
            'Product Type',
            'SKU',
            'Name',
            'Brand',
            'Category',
            'Inventory Tracking',
            'Price',
            'Publish Datetime',
            'Unpublish Datetime',
            'Product Tag',
            'Description',
            'Channels',
            'Groups Products',
            'Bundled Products',
            'Is Digital Goods',
            'Is Track Inventory',
            'Is Use OMSVoucher',
            'Days To Expiry',
            'Status',
        ];
    }

    public function array(): array
    {
        return $this->products;
    }
}