<?php
namespace App\Http\Requests\Api\Mobile\Products;

use App\Http\Requests\BaseFormRequest;

/**
 * @OA\Schema(
 *   schema="SearchRequest",
 *   type="object",
 *   @OA\Property(property="q", type="string", description="Search keyword"),
 *   @OA\Property(property="sort_by", type="string", enum={"top_sales","latest","low_to_high","high_to_low","oldest"}, description="Sort option"),
 *   @OA\Property(property="min_price", type="number", format="float", minimum=0, description="Minimum price"),
 *   @OA\Property(property="max_price", type="number", format="float", minimum=0.01, description="Maximum price"),
 *   @OA\Property(property="rate", type="integer", enum={1,2,3,4,5}, description="Product rate"),
 *   @OA\Property(
 *     property="category_ids",
 *     type="array",
 *     @OA\Items(type="integer"),
 *     description="List of category IDs"
 *   ),
 *   @OA\Property(
 *     property="subcategory_ids",
 *     type="array",
 *     @OA\Items(type="integer"),
 *     description="List of subcategory IDs"
 *   ),
 *   @OA\Property(
 *     property="subsubcategory_ids",
 *     type="array",
 *     @OA\Items(type="integer"),
 *     description="List of subsubcategory IDs"
 *   ),
 *   @OA\Property(
 *     property="brand_ids",
 *     type="array",
 *     @OA\Items(type="integer"),
 *     description="List of brand IDs"
 *   ),
 *   @OA\Property(property="coupon_id", type="integer", description="Coupon ID"),
 *   @OA\Property(
 *     property="seller_ids",
 *     type="array",
 *     @OA\Items(type="integer"),
 *     description="List of seller IDs"
 *   ),
 *   @OA\Property(property="page", type="integer", minimum=1, description="Page number"),
 *   @OA\Property(property="per_page", type="integer", description="Items per page"),
 *   @OA\Property(property="discount", type="integer", enum={1}, description="Discount filter"),
 *   @OA\Property(
 *     property="attributes",
 *     type="array",
 *     @OA\Items(
 *       type="object",
 *       @OA\Property(property="id", type="integer", description="Attribute ID"),
 *       @OA\Property(property="value", type="string", description="Attribute value")
 *     ),
 *     description="Product attributes"
 *   )
 * )
 */
class SearchRequest extends BaseFormRequest {
    // top_sales, latest, low_to_high, high_to_low, oldest
    protected $rules = [
        'q' => ['nullable', 'string'],
        'sort_by' => ['string', 'in:top_sales,latest,low_to_high,high_to_low,oldest'],
        'min_price' => ['numeric', 'min:0'],
        'max_price' => ['numeric', 'min:0.01', 'gte:min_price'],
        'rate' => ['integer', 'in:1,2,3,4,5'],
        'category_ids' => ['array'],
        'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
        'subcategory_ids' => ['array'],
        'subcategory_ids.*' => ['integer', 'distinct', 'exists:sub_categories,id'],
        'subsubcategory_ids' => ['array'],
        'subsubcategory_ids.*' => ['integer', 'distinct', 'exists:sub_sub_categories,id'],
        'brand_ids' => ['array'],
        'brand_ids.*' => ['integer', 'distinct', 'exists:brands,id'],
        'coupon_id' => ['integer', 'exists:coupons,id'],
        'seller_ids' => ['array'],
        'seller_ids.*' => ['integer', 'distinct', 'exists:sellers,id'],
        'page' => ['integer', 'min:1'],
        'per_page' => ['integer'],
        'discount' => ['integer', 'in:1'],
        'attributes' => ['array'],
        'attributes.*.id' => ['required', 'integer'],
        'attributes.*.value' => ['required', 'string'],
    ];
} 