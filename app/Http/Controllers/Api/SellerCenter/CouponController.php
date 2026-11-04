<?php
namespace App\Http\Controllers\Api\SellerCenter;

use App\Http\Resources\SellerCenter\CouponCollection;
use App\Http\Resources\SellerCenter\Coupon as CouponResource;
use App\Models\Coupon;
use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SellerCenter\Coupon\CreateRequest;
use App\Http\Requests\Api\SellerCenter\Coupon\UpdateRequest;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CouponController extends Controller
{
    const SUPPORTED_SWITCH_FIELDS = ['is_active', 'published'];
    /**
     * @param CouponService
     */
    protected $service;

    public function __construct(CouponService $couponService) {
        $this->service = $couponService;
    }

    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $data = $request->all();
            $user = $request->user();

            if ($data['type'] === Coupon::TYPE_PRODUCT_BASE && empty($data['details']['products'])) {
                return $this->errorResponse(__('Need to choose products to apply this coupon'));
            }

            $data['owner'] = Coupon::OWNER_SELLER;
            $data['seller_id'] = $user->shop->id;

            $entity = $this->service->create($data);

            return $this->successResponse($entity);
        } catch (\Throwable $th) {
            return $this->errorResponse(__('Has error when create coupon'));
        }
        
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = new CouponCollection($this->service->getListForSeller($user, $request));

        return $this->successResponse($data);
    }

    public function update(UpdateRequest $request, Coupon $coupon): JsonResponse
    {
        $data = $request->all();

        $coupon->fill($data);
        if ($coupon->isDirty()) {
            $changes = $coupon->getDirty();

            foreach ($changes as $key => $change) {
                if ($key == 'code' && $this->service->getFirstWithConditions(['code' => $data['code']])) {
                    return $this->errorResponse(__('This coupon is already on system!'));
                }
                if ($key == 'start_date') {
                    $coupon->start_date = Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay()->timestamp;
                }
                if ($key == 'end_date') {
                    $coupon->end_date = Carbon::createFromFormat('Y-m-d', $data['end_date'])->endOfDay()->timestamp;
                }
            }

            try {
                $coupon->save();
            } catch (\Exception $e) {
                return $this->errorResponse(__('Has error when update coupon'));
            }
        }

        return $this->successResponse($coupon);
    }

    public function show(Request $request, Coupon $coupon): JsonResponse
    {
        $user = $request->user();
        if (Coupon::OWNER_SELLER !== $coupon->owner && $coupon->seller_id !== $user->shop->id) {
            return $this->errorResponse(__('Coupon not found'), ResponseAlias::HTTP_FORBIDDEN);
        }
        return $this->successResponse(new CouponResource($coupon));
    }

    /**
     * @param  Request  $request
     * @param  Coupon  $coupon
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleField(Request $request, Coupon $coupon): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (!in_array($key, self::SUPPORTED_SWITCH_FIELDS)) {
                unset($data[$key]);
            }
        }

        if (empty($data)) {
            return $this->errorResponse(__('No field to update'));
        }

        $this->service->update($coupon->id, $data);
        return $this->successResponse([]);
    }

}