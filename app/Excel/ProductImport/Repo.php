<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 28/04/2022
 * Time: 01:41
 */

namespace App\Excel\ProductImport;

use App\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

/**
 * Class Repo
 * @package App\Excel\ProductImport
 * @property
 */
class Repo
{
    protected $existProductOwner = [];
    protected $existBrand = [];
    protected $existCategory = [];
    protected $existSubCategory = [];
    protected $existSubSubCategory = [];

    const STATUS = [
        'Active' => 1,
        'Inactive' => 0,
    ];

    const DISCOUNT_TYPE = [
        '$' => Product::DISCOUNT_TYPE_AMOUNT,
        '%' => Product::DISCOUNT_TYPE_PERCENT,
    ];

    const SPLIT_SYMBOL = '-->';

    public function getProductById($productId) {
        $product = DB::select("
            SELECT 
                *
            FROM
                products
            WHERE
                id = :productId
            ",
            [ ":productId" => $productId ]
        );

        if ($product) {
            return $product[0];
        }
        return false;
    }

    public function getBrandIdByName($name) {
        if (isset($this->existBrand[$name])) {
            return $this->existBrand[$name];
        }

        $brand = DB::select("
            SELECT 
                id, name
            FROM
                brands
            WHERE
                name = :name
            ",
            [ ":name" => $name ]
        );

        if ($brand) {
            $this->existBrand[$name] = $brand[0]->id;
            return $brand[0]->id;
        }
        return 0;
    }

    public function getCatIdByName($name) {
        if (isset($this->existCategory[$name])) {
            return $this->existCategory[$name];
        }

        $cat = DB::select("
            SELECT 
                id, name
            FROM
                categories
            WHERE
                name = :name
            ",
            [ ":name" => $name ]
        );

        if ($cat) {
            $this->existCategory[$name] = $cat[0]->id;
            return $cat[0]->id;
        }
        return 0;
    }

    public function getSubCatIdByName($name, $parentId) {
        $cachedKey = $parentId.self::SPLIT_SYMBOL.$name;
        if (isset($this->existSubCategory[$cachedKey])) {
            return $this->existSubCategory[$cachedKey];
        }

        $cat = DB::select("
            SELECT 
                sub_categories.id, sub_categories.name
            FROM
                categories
            LEFT JOIN 
                sub_categories 
                    on categories.id = sub_categories.category_id
            WHERE
                sub_categories.name = :name 
                AND categories.id = :parentId 
            ",
            [ ":name" => $name, ":parentId" => $parentId ]
        );

        if ($cat) {
            $this->existSubCategory[$cachedKey] = $cat[0]->id;
            return $cat[0]->id;
        }
        return 0;
    }

    public function getSubSubCatIdByName($name, $parentId) {
        $cachedKey = $parentId.self::SPLIT_SYMBOL.$name;
        if (isset($this->existSubSubCategory[$cachedKey])) {
            return $this->existSubSubCategory[$cachedKey];
        }

        $cat = DB::select("
            SELECT 
                sub_sub_categories.id, sub_sub_categories.name
            FROM
                sub_categories
            LEFT JOIN 
                sub_sub_categories 
                    on sub_categories.id = sub_sub_categories.sub_category_id
            WHERE
                sub_sub_categories.name = :name 
                AND sub_categories.id = :parentId 
            ",
            [ ":name" => $name, ":parentId" => $parentId ]
        );

        if ($cat) {
            $this->existSubSubCategory[$cachedKey] = $cat[0]->id;
            return $cat[0]->id;
        }
        return 0;
    }

    private function updateProduct($data) {

        try {
            // $status = DB::update("
            // UPDATE 
            //     products
            // SET
            //     name = :name,
            //     category_id = :category_id,
            //     subcategory_id = :subcategory_id,
            //     subsubcategory_id = :subsubcategory_id,
            //     brand_id = :brand_id,
            //     description = :description,
            //     unit_price = :unit_price,
            //     purchase_price = :purchase_price,
            //     variant_product = :variant_product,
            //     choice_options = :choice_options,
            //     current_stock = :current_stock,
            //     unit = :unit,
            //     published = :published,
            //     min_qty = :min_qty,
            //     max_qty = :max_qty,
            //     max_qty_start = :max_qty_start,
            //     max_qty_end = :max_qty_end,
            //     discount = :discount,
            //     discount_type = :discount_type,
            //     updated_at = :updated_at
            // WHERE
            //     id = :id
            // ",
            //     [
            //         ":id" => $data['product_id'],
            //         ":name" => $data['product_name'],
            //         ":category_id" => $data['category_id'],
            //         ":subcategory_id" => $data['sub_category_id'],
            //         ":subsubcategory_id" => $data['sub_sub_category_id'],
            //         ":brand_id" => $data['brand_id'],
            //         ":description" => $data['product_description_min_50_words'],
            //         ":unit_price" => (float)$data['unit_price'],
            //         ":purchase_price" => (float)$data['unit_price'],
            //         ":variant_product" => $data['variant_product'],
            //         ":choice_options" => $data['choice_options'],
            //         ":current_stock" => (int)$data['stock'],
            //         ":unit" => $data['product_sku'],
            //         ":published" => $this->mapStatus($data['status']),
            //         ":min_qty" => $data['minimum_purchase_quantity'],
            //         ":max_qty" => $data['maximum_purchase_quantity']??0,
            //         ":max_qty_start" => $data['maxQtyStart'],
            //         ":max_qty_end"   => $data['maxQtyEnd'],
            //         ":discount" => $data['discount_given']?(float)$data['discount_given']:null,
            //         ":discount_type" => $this->mapDiscountType($data['discount_type']),
            //         ":updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            //     ]);

            $productData = [
                'name'              => $data['product_name'],
                'category_id'       => $data['category_id'],
                'subcategory_id'    => $data['sub_category_id'],
                'subsubcategory_id' => $data['sub_sub_category_id'],
                'brand_id'          => $data['brand_id'],
                // 'description'       => $data['product_description_min_50_words'],
                'unit_price'        => (float) $data['unit_price'],
                'purchase_price'    => (float) $data['unit_price'],
                'variant_product'   => $data['variant_product'],
                'choice_options'    => $data['choice_options'],
                'current_stock'     => (int) $data['stock'],
                'unit'              => strtoupper($data['product_sku']),
                'published'         => $this->mapStatus($data['status']),
                'min_qty'           => $data['minimum_purchase_quantity'],
                'max_qty'           => $data['maximum_purchase_quantity'] ?? 0,
                'max_qty_start'     => $data['maxQtyStart'],
                'max_qty_end'       => $data['maxQtyEnd'],
                'discount'          => $data['discount_given'] ? (float) $data['discount_given'] : null,
                'discount_type'     => $this->mapDiscountType($data['discount_type']),
            ];

            if (!empty($data['product_description_min_50_words'])) {
                $productData['description'] = $data['product_description_min_50_words'];
            }

            $status = Product::findOrFail($data['product_id'])
                ->update($productData);

            return $data['product_id'];
        } catch (\Exception $e) {
            return $e;
        }
    }

    private function mapStatus($status) {
        $status = ucfirst(strtolower($status));
        $publishedStatus = self::STATUS;
        return $publishedStatus[$status]??0;
    }

    private function mapDiscountType($discount) {
        return self::DISCOUNT_TYPE[$discount]??null;
    }

    private function insertProduct($data) {

        try {
            $time = Carbon::now()->format('Y-m-d H:i:s');

            $productData = [
                "name" => $data['product_name'],
                "added_by" => Product::ADD_BY_SELLER,
                "user_id" => $data['user_id'],
                "category_id" => $data['category_id'],
                "subcategory_id" => $data['sub_category_id'],
                "subsubcategory_id" => $data['sub_sub_category_id'],
                "brand_id" => $data['brand_id'],
                // "description" => $data['product_description_min_50_words'],
                "unit_price" => (float) $data['unit_price'],
                "purchase_price" => (float) $data['unit_price'],
                "variant_product" => $data['variant_product'],
                "choice_options" => $data['choice_options'],
                "colors" => $data['colors'],
                "current_stock" => $data['stock'] ?? 0,
                "unit" => strtoupper($data['product_sku']),
                "published" => $this->mapStatus($data['status']),
                "min_qty" => $data['minimum_purchase_quantity'],
                "max_qty" => $data['maximum_purchase_quantity'] ?? 0,
                "max_qty_start" => $data['maxQtyStart'],
                "max_qty_end"   => $data['maxQtyEnd'],
                "discount" => $data['discount_given'],
                "discount_type" => $this->mapDiscountType($data['discount_type']),
                "slug" => $data['slug'],
                "frontend_sorting" => $data['frontend_sorting'],
                "created_at" => $time,
                "updated_at" => $time,
            ];

            if (!empty($data['product_description_min_50_words'])) {
                $productData['description'] = $data['product_description_min_50_words'];
            }

            $product = Product::create($productData);

            return $product->id;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function saveProduct($data) {

        if ($data['product_id']) {
            return $this->updateProduct($data);
        } else {
            return $this->insertProduct($data);
        }
    }

    public function getAllowAttributes($user) {
        $post = DB::select("
            SELECT 
                attributes.id, attributes.name
            FROM
                attribute_shop
            LEFT JOIN 
                attributes
                    on attribute_shop.attribute_id = attributes.id
            WHERE
                attribute_shop.shop_id = (:shop_id)
            ",
            [ ":shop_id" => $user->shop->id ]
        );

        if ($post) {
            $collection = collect($post);

            $plucked = $collection->mapWithKeys(function ($item) {
                return [$item->id => $item->name];
            });
            return $plucked->all();
        }
        return [];
    }

    public function getVariations($productId) {
        $query = DB::table('product_stocks')
            ->where('product_id', $productId);
        return $query->get();
    }

    public function updateVariation($id, $data) {
        $setQuery = "SET
                variant = :variant,
                sku = :sku,
                price = :price,
                final_price = :final_price,
                discount = :discount,
                discount_type = :discount_type,
                qty = :qty,
                updated_at = :updated_at";

        $args = [
            ":id" => $id,
            ":variant" => $data['variant'],
            ":sku" => strtoupper($data['product_sku']),
            ":price" => $data['unit_price'],
            ":final_price" => calculateFinalPriceBaseOnDiscount($data['discount_given'], $this->mapDiscountType($data['discount_type']), $data['unit_price']),
            ":discount" => $data['discount_given'] ? (float)$data['discount_given'] : null,
            ":discount_type" => $data['discount_type'] ? $this->mapDiscountType($data['discount_type']) : null,
            ":qty" => $data['stock'],
            ":updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ];

        $query = "
            UPDATE 
                `product_stocks`
            $setQuery
            WHERE
                id = :id
            ";

        try {
            $status = DB::update($query, $args
                );
            return (bool) $status;
        } catch (\Exception $e) {
            $message = 'Variation SKU '.$data['product_sku'].' : '.$e->getMessage();
            return new \Exception($message);
        }
    }

    public function deleteVariations($product_id, $skus) {

        try {
            $status = DB::delete("
            DELETE FROM `product_stocks`
            WHERE product_id = :product_id
                AND sku IN (:sku)
            ",
                [
                    ":product_id" => $product_id,
                    ":sku" => implode(',', $skus),
                ]);
            return (bool) $status;
        } catch (\Exception $e) {
            $message = 'Can not remove old variation : '.$e->getMessage();
            return new \Exception($message);
        }
    }

    public function insertVariation($data, $productId) {

        try {
            $time = Carbon::now()->format('Y-m-d H:i:s');

            return DB::insert("
                INSERT INTO 
                    `product_stocks`
                        (product_id, variant, sku, price, qty, discount, discount_type, final_price, created_at, updated_at)
                VALUES
                    (:product_id, :variant, :sku, :price, :qty, :discount, :discount_type, :final_price, :created_at, :updated_at)
                ",
                [
                    ":product_id" => $productId,
                    ":variant" => $data['variant'],
                    ":sku" => strtoupper($data['product_sku']),
                    ":price" => $data['unit_price'],
                    ":final_price" => calculateFinalPriceBaseOnDiscount($data['discount_given'], $this->mapDiscountType($data['discount_type']), $data['unit_price']),
                    ":discount" => $data['discount_given'] ?? null,
                    ":discount_type" => $data['discount_type'] ? $this->mapDiscountType($data['discount_type']) : null,
                    ":qty" => $data['stock'],
                    ":created_at" => $time,
                    ":updated_at" => $time,
                ]
            );
        } catch (\Exception $e) {
            $message = 'Variation SKU '.$data['product_sku'].' : '.$e->getMessage();
            return new \Exception($message);
        }
    }
}