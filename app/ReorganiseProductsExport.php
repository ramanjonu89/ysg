<?php
namespace App;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReorganiseProductsExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return DB::table('products')
        ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('sub_categories', 'products.subcategory_id', '=', 'sub_categories.id')
        ->leftJoin('sub_sub_categories', 'products.subsubcategory_id', '=', 'sub_sub_categories.id')
        ->leftJoin('users', 'products.user_id', '=', 'users.id')
        ->select(
            'products.id as product_id', 
            'products.name as product_name',
            'products.published as status',
            'categories.id as category_id', 
            'categories.name as category_name',
            'sub_categories.id as sub_category_id', 
            'sub_categories.name as sub_category_name',
            'sub_sub_categories.id as sub_sub_category_id', 
            'sub_sub_categories.name as sub_sub_category_name',
            'users.name as store_name', 
        )
        ->get();
    }

    public function headings(): array
    {
        return [
            'Product Id',
            'Product Name',
            'Category Id',
            'Category Name',
            'Sub Category Id',
            'Sub Category Name',
            'Sub SubCategory Id',
            'Sub SubCategory Name',
            'Status',
            'Store Name'
        ];
    }

    /**
     * @var Product $product
     */
    public function map($product): array
    {
        return [
            $product->product_id,
            $product->product_name,
            $product->category_id,
            $product->category_name,
            $product->sub_category_id,
            $product->sub_category_name,
            $product->sub_sub_category_id,
            $product->sub_sub_category_name,
            $product->status === 1 ? 'enable' : 'disable',
            $product->store_name
        ];
    }
}

