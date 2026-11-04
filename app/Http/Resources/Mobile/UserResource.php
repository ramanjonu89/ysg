<?php

namespace App\Http\Resources\Mobile;

use App\Services\CartService;
use App\Services\CorporateAccessService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="UserProfile",
 *     type="object",
 *     title="User Profile",
 *     description="Detailed user profile resource with corporate and counter info",
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         description="User basic information",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="john@example.com"),
 *         @OA\Property(property="country_code", type="string", nullable=true, example="+65"),
 *         @OA\Property(property="phone", type="string", nullable=true, example="0123456789"),
 *         @OA\Property(property="avatar_original", type="string", nullable=true, example="https://example.com/uploads/users/avatar.jpg"),
 *         @OA\Property(property="referral_code_register", type="string", nullable=true, example="ABC123XYZ"),
 *         @OA\Property(property="is_update_email", type="boolean", example=false),
 *         @OA\Property(
 *              property="corporate_access",
 *              type="object",
 *              @OA\Property(property="can_share", type="boolean", example=true),
 *              @OA\Property(property="share_limit", type="integer", example=5),
 *              @OA\Property(property="share_left", type="integer", example=3)
 *         ),
 *         @OA\Property(property="is_corporate_access_pass", type="boolean", example=false)
 *     ),
 *     @OA\Property(
 *         property="counters",
 *         type="object",
 *         description="Summary counters for user's activities",
 *         @OA\Property(property="cart", type="integer", example=10),
 *         @OA\Property(property="wishlist", type="integer", example=2),
 *         @OA\Property(property="orders", type="integer", example=5)
 *     )
 * )
 */
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $cartService  = app(CartService::class);
        $cart = $cartService->getCart();
        $totalItems = $cart->getTotalItems();

        $user = Auth::guard('api')->user();
        $corporateAccessService   = app(CorporateAccessService::class);

        $corporateData = $this->corporateData($user, $corporateAccessService);
        $isCAPass = $corporateAccessService->getSharedShops($user)->count();

        return [
            'user' => [
                'id'     => $this->id,
                'name'   => $this->name,
                'email'  => $this->email,
                'country_code' => $this->country_code,
                'phone'  => $this->phone,
                'avatar_original' => $this->avatar_original ? url($this->avatar_original) : null,
                'referral_code_register' => $this->referral_code_register,
                'is_update_email'   => $this->is_update_email ? true : false,
                'corporate_access' => isset($corporateData['can_share']) && $corporateData['can_share'] ? $corporateData : null,
                'is_corporate_access_pass' => $isCAPass ? true : false,
            ],

            'counters' => [
                'cart'     => $totalItems,
                'wishlist' => $this->wishlists()->count(),
                'orders'   => $this->orders()->count(),
            ],
        ];
    }

    private function corporateData($user, $corporateAccessService)
    {
        $data = [];
        $corporateRefEnabled = $corporateAccessService ->isSettingEnable();
        $canShareCA = $corporateAccessService ->canShare($user);

        if ($corporateRefEnabled && $canShareCA) {
            $corporateRefLimit = $corporateAccessService ->getUserShareLimit($user);
            $corporateRefLeft = $corporateAccessService ->getUserShareTimesLeft($user);
            $data = [
                'can_share' => $canShareCA,
                'share_limit' => $corporateRefLimit,
                'share_left' => $corporateRefLeft,
            ];
        }

        return $data;
    }
}