<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;
use App\ObjectModels\Coupon as ObjectModelsCoupon;
use App\Services\CartService;

/**
 * @OA\Schema(
 *     schema="Coupon",
 *     type="object",
 *     title="Coupon",
 *     description="Coupon resource for mobile application",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the coupon",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Coupon title",
 *         example="20% Off Summer Sale"
 *     ),
 *     @OA\Property(
 *         property="spending",
 *         type="string",
 *         description="Minimum spending requirement and maximum discount information",
 *         example="Min. Spending: $50.00, Max. Discount: $20.00"
 *     ),
 *     @OA\Property(
 *         property="use_for",
 *         type="string",
 *         description="Products that the coupon can be used for",
 *         example="All Products"
 *     ),
 *     @OA\Property(
 *         property="use_before",
 *         type="string",
 *         description="Coupon expiration date in formatted string",
 *         example="Use before: 2024-08-31"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         nullable=true,
 *         description="Coupon description",
 *         example="Get 20% off on all items. Valid until end of summer."
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="string",
 *         description="Coupon code",
 *         example="SUMMER20"
 *     ),
 *     @OA\Property(
 *         property="can_applied",
 *         type="boolean",
 *         description="Whether the coupon can be applied to current cart",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="valid_until",
 *         type="string",
 *         description="Coupon expiration date",
 *         example="2024-08-31 23:59:59"
 *     )
 * )
 */
class CouponResource extends JsonResource
{
    public function toArray($request)
    {
        $details = json_decode($this->details);
        
        if (isset($details->title)) {
            $title = $details->title;
        }
        
        $description = null;
        if (isset($details->description)) {
            $description = $details->description;
        }

        $title = __('coupon.title', ['offValue' => $this->discount_label]);

        if ($this->min_buy && $this->max_discount) {
            $spending = __('coupon.has_min_buy_and_max_discount', ['min_buy' => format_price($this->min_buy), 'max_discount' => format_price($this->max_discount)]);
        } elseif ($this->min_buy) {
            $spending = __('coupon.min_buy', ['value' => format_price($this->min_buy)]);
        } elseif ($this->max_discount) {
            $spending = __('coupon.max_discount', ['value' => format_price($this->max_discount)]);
        } else {
            $spending = __('No Min. Spending');
        }

        $use_for = ($this->type === 'cart_base' && (!$this->shop_ids || in_array(0, $this->shop_ids))) ? __('All Products') : __('Selected Products Only');

        $useBefore = __('coupon.use_before', ['date' => $this->end_date_formatted]);

        $cart = app(CartService::class)->getTempCart();
        $is_can_apply = false;
        $couponObject = new ObjectModelsCoupon($this->resource);
        $validationResult = $cart->simpleValidateCoupon($couponObject);
        if ($validationResult['valid']) {
            $is_can_apply = true;
        }
        
        return [
            'id' => $this->id,
            'title' => $title,
            'spending' => $spending,
            'use_for' => $use_for,
            'use_before' => $useBefore,
            'description' => $description,
            'code' => $this->code,
            'can_applied' => $is_can_apply,
            'valid_until' => date('Y-m-d H:i:s', $this->end_date),
        ];
    }
}

