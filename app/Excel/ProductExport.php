<?php

namespace App\Excel;

use App\Models\Product;
use App\Models\ProductStock;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class ProductExport extends DefaultValueBinder implements WithCustomValueBinder, FromCollection, WithMapping, WithHeadings, WithColumnFormatting
{
    const MAX_RECORD = 500;
    protected $user;
    protected $productIds;

    public function __construct(
        User $user,
        $productIds
    ) {
        $this->user = $user;
        $this->productIds = $productIds;
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
        return $this->prepareCollections();
    }

    private function prepareCollections() {
        $query = $this->buildQuery();
//        $query->limit(self::MAX_RECORD);
        $queryResults = $query->get()->groupBy('product_id');

        $result = collect();

        foreach ($queryResults as $group) {
            $product = clone $group->first();
            $product->variation_id = null;
            $product->variation_name = null;
            $product->variation_sku = null;
            $product->variation_price = null;
            $product->variation_qty = null;
            $product->variantion_discount_type = null;
            $product->variantion_discount = null;

            if (is_null($product->discount_given) || $product->discount_given <= 0) {
                $product->discount_type = null;
            }

            if ($group->count() >= 1 && !empty(json_decode($product->variation_options))) {

                $product->unit_price = null;
                $product->stock = null;
                $product->discount_type  = null;
                $product->discount_given = null;
                $result->push($product);

                try {
                    $mapVariation = $this->getProductOptions($product->variation_options);

                    foreach ($group as $variation) {

                        $variationValues = explode(ProductStock::SPLIT_SYMBOL, $variation->variation_name);

                        if (isset($mapVariation[$variationValues[0]])) {
                            $variation->variation_1 = $mapVariation[$variationValues[0]];
                            $variation->variation_option_1 = $variationValues[0];
                        }

                        if (isset($variationValues[1])) {
                            if (isset($mapVariation[$variationValues[1]])) {
                                $variation->variation_2 = $mapVariation[$variationValues[1]];
                                $variation->variation_option_2 = $variationValues[1];
                            }
                        }

                        $variation->product_id = null;
                        $variation->product_name = null;
                        $variation->product_description = null;
                        $variation->sku = $variation->variation_sku;
                        $variation->brand = null;
                        $variation->category = null;
                        $variation->sub_category = null;
                        $variation->sub_sub_category = null;
                        $variation->status = null;
                        $variation->unit_price = $variation->variation_price;
                        $variation->stock = $variation->variation_qty;
                        $variation->discount_type = $variation->variation_discount_type;
                        $variation->discount_given = $variation->variation_discount_given;
                        $variation->minimum_purchase_quantity = null;
                        $variation->maximum_purchase_quantity = null;
                        $variation->max_qty_start_date = null;
                        $variation->max_qty_end_date = null;

                        $result->push($variation);
                    }
                } catch (\Exception $e) {

                }

            } else {
                $result->push($product);
            }
        }

        return $result;
    }

    private function getProductOptions($json) {
        $result = [];
        $options = json_decode($json);
        foreach ($options as $option) {
            foreach ($option->values as $value) {
                $result[$value] = $option->attribute->text;
            }
        }

        return $result;
    }

    public function buildQuery()
    {
        $query = DB::table('products')

            ->select(
                'products.id as product_id',
                'products.unit as sku',
                'products.unit as group_id',
                'products.name as product_name',
                'products.description as product_description',
                'brands.name as brand',
                'categories.name as category',
                'sub_categories.name as sub_category',
                'sub_sub_categories.name as sub_sub_category',
                'products.published as status',
                'products.unit_price as unit_price',
                'products.current_stock as stock',
                'products.discount_type as discount_type',
                'products.discount as discount_given',
                'products.min_qty as minimum_purchase_quantity',
                'products.max_qty as maximum_purchase_quantity',
                'products.max_qty_start as max_qty_start_date',
                'products.max_qty_end as max_qty_end_date',
                'product_stocks.id as variation_id',
                'product_stocks.variant as variation_name',
                'product_stocks.sku as variation_sku',
                'product_stocks.price as variation_price',
                'product_stocks.qty as variation_qty',
                'product_stocks.discount_type as variation_discount_type',
                'product_stocks.discount as variation_discount_given',
                'products.choice_options as variation_options',
            )

            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('sub_categories', 'products.subcategory_id', '=', 'sub_categories.id')
            ->leftJoin('sub_sub_categories', 'products.subsubcategory_id', '=', 'sub_sub_categories.id')
            ->leftJoin('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->where('products.unit', 'NOT LIKE', "deleted-%");
        ;

        if ($this->productIds) {
            $query->whereIn('products.id', $this->productIds);
            //            ->whereIn('products.id', [13790])
        } else {
            $query->where('products.user_id', $this->user->id);
        }

        $query->orderBy('products.created_at', 'asc');

        return $query;
    }


    public function headings(): array
    {
        return [
            'Product ID',
            'Product SKU',
            'Group ID',
            'Product Name*',
            'Product Description* (Min. 50 Words)',
            'Brand',
            'Category*',
            'Sub Category',
            'Sub Sub Category',
            'Status*',
            'Variation 1',
            'Variation Option 1',
            'Variation 2',
            'Variation Option 2',
            'Unit Price*',
            'Stock*',
            'Discount type (% / $)',
            'Discount Given',
            'Minimum Purchase Quantity',
            'Maximum Purchase Quantity',
            'Maximum Purchase Quantity - Start Date',
            'Maximum Purchase Quantity - End Date',
        ];
    }

    /**
     * @var $entity
     */
    public function map($entity): array
    {
        return [
            $entity->product_id,
            $entity->sku,
            $entity->group_id,
            $entity->product_name,
            $entity->product_description,
            $entity->brand??null,
            $entity->category,
            $entity->sub_category??null,
            $entity->sub_sub_category??null,
            $this->getStatus($entity->status),
            $entity->variation_1??null,
            $entity->variation_option_1??null,
            $entity->variation_2??null,
            $entity->variation_option_2??null,
            $entity->unit_price,
            $entity->stock,
            $this->getDiscountType($entity->discount_type),
            $entity->discount_given,
            $entity->minimum_purchase_quantity,
            $entity->maximum_purchase_quantity,
            $this->formatDateTime($entity->max_qty_start_date),
            $this->formatDateTime($entity->max_qty_end_date),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    private function getStatus($status) {
        if ($status === 1) {
            return 'Active';
        } else if ($status === 0) {
            return 'Inactive';
        }
        return null;
    }

    private function getDiscountType($discountType) {
        return $discountType?($discountType === Product::DISCOUNT_TYPE_AMOUNT)?'$':'%':null;
    }

    private function formatDateTime($date) {
        if (isset($date)) {
            return Carbon::parse($date)->format('d/m/Y');
        }

        return null;
    }
}
