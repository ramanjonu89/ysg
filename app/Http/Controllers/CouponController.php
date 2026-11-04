<?php
namespace App\Http\Controllers;

use Exception;
use App\Models\{Coupon,
    SubCategory,
    SubSubCategory
};
use App\Services\{CategoryService,
    CouponService,
    ShopService,
    ProductService,
    SubCategoryService,
    SubSubCategoryService
};
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Excel\CouponExport;

class CouponController extends Controller
{
    /**
     * @param CategoryService
     */
    protected $categoryService;

    /**
     * @param CouponService
     */
    protected $couponService;

    /**
     * @param ShopService
     */
    protected $shopService;

    /**
     * @param ProductService
     */
    protected $productService;

    /**
     * @param SubCategoryService
     */
    protected $subCateService;

    /**
     * @param SubSubCategoryService
     */
    protected $subSubCateService;

    public function __construct(
        CategoryService     $categoryService,
        CouponService       $couponService,
        ShopService         $shopService,
        ProductService      $productService,
        SubCategoryService  $subCateService,
        SubSubCategoryService $subSubCateService
    ) {
        $this->categoryService   = $categoryService;
        $this->couponService     = $couponService;
        $this->shopService       = $shopService;
        $this->productService    = $productService;
        $this->subCateService    = $subCateService;
        $this->subSubCateService = $subSubCateService;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $is_cash_voucher = $request->get('is_cash_voucher', '');

        $type = [];
        if ($is_cash_voucher) {
            array_push($type, Coupon::TYPE_CASH_BASE);
        } else {
            array_push($type, Coupon::TYPE_PRODUCT_BASE, Coupon::TYPE_CART_BASE, Coupon::TYPE_CATEGORY_BASE);
        }

        $params = [
            'type' => $type,
            'per_page' => 50,
            'page' => $request->get('page', 1)
        ];
        
        if ($request->has('search') && !empty($request->get('search'))) {
            $params['code'] = $request->get('search');
        }
        
        $coupons = $this->couponService->getList($params);

        return view('coupons.index', compact('coupons', 'is_cash_voucher'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $is_cash_voucher = $request->is_cash_voucher;
        $is_generation_bulk = $request->is_generation_bulk;
        return view('coupons.create', compact('is_cash_voucher','is_generation_bulk'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $count = 0;
        $couponCodes = [];
        $isGenerationBulk = $request->is_generation_bulk;
        if ($this->checkCouponCodeExists($request->coupon_code) && !$isGenerationBulk) {
            flash(__('Coupon already exist for this coupon code'))->error();
            return back();
        }
        if (!isset($request->coupon_type) || !in_array($request->coupon_type, ['product_base','cart_base','category_base','cash_base'])) {
            flash(__('Coupon Type  does not exist'))->error();
            return back();
        }
        if ($isGenerationBulk) {
            $maxCouponToGenerate = $request->max_coupon_to_generate;
            while ($count < $maxCouponToGenerate) {
                $couponCode = $request->coupon_code . '_' . strtoupper(Str::random(9));
                if (!$this->checkCouponCodeExists($couponCode)) {
                    $couponCodes[] = $couponCode;
                    $count++;
                }
            }
        } else {
            $couponCodes[] = $request->coupon_code;
        }

        try {
            foreach ($couponCodes as $key => $couponCode) {
                $couponInputs = $this->formatDataInputs($couponCode, $request, $key);
                $coupon = $this->couponService->create($couponInputs);
            }

            if ($coupon) {
                if ($count) {
                    flash(__($count . ' coupons have been saved successfully'))->success();
                } else {
                    flash(__('Coupon has been saved successfully'))->success();
                }

                if ($coupon->type === Coupon::TYPE_CASH_BASE) {
                    return redirect()->route('coupon.index', ['is_cash_voucher' => '1']);
                }

                return redirect()->route('coupon.index');
            } else {
                flash(__('Something went wrong'))->warning();
                return back();
            }
        }catch(Exception $e){
            flash($e->getMessage())->error();
            return back();
        }
    }
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $coupon = Coupon::findOrFail($id);

        return view('coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id){
        if(!isset($request->coupon_type) || !in_array($request->coupon_type, ['product_base','cart_base','category_base','cash_base'])){
            flash(__('Coupon Type  does not exist'))->error();
            return back();
        }

        $coupon = $this->couponService->findBy('id', $id);
        $coupon->fill($request->all());
        if ($coupon->isDirty('code') && $this->checkCouponCodeExists($request->code)) {
            flash(__('Coupon already exist for this coupon code'))->error();
            return back();
        }

        try {
            $couponCode = $request->code;
            $couponInputs = $this->formatDataInputs($couponCode, $request, []);
            $couponUpdate = $this->couponService->updateCoupon($id, $couponInputs);
            if ($couponUpdate) {
                flash(__('Coupon has been saved successfully'))->success();

                if ($couponUpdate->type === Coupon::TYPE_CASH_BASE) {
                    return redirect()->route('coupon.index', ['is_cash_voucher' => '1']);
                }

                return redirect()->route('coupon.index');
            } else {
                flash(__('Something went wrong'))->error();
                return back();
            }
        } catch(Exception $e){
            flash($e->getMessage())->error();
            return back();
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            if ($coupon->type === Coupon::TYPE_CASH_BASE) {
                $this->couponService->destroy($id);
                flash(__('Coupon has been deleted successfully'))->success();
                return redirect()->route('coupon.index', ['is_cash_voucher' => '1']);
            } else {
                $this->couponService->destroy($id);
                flash(__('Coupon has been deleted successfully'))->success();
                return redirect()->route('coupon.index');
            }
        } catch (\Throwable $th) {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function get_coupon_form(Request $request)
    {
        $is_generation_bulk = $request->is_generation_bulk;
        if ($request->coupon_type == Coupon::TYPE_PRODUCT_BASE) {
            $categories = $this->categoryService->getAllList();
            return view('partials.coupons.product_base_coupon', compact('categories', 'is_generation_bulk'));
        } elseif ($request->coupon_type == Coupon::TYPE_CART_BASE) {
            $shops = $this->shopService->getListForCoupon();
            return view('partials.coupons.cart_base_coupon', compact('is_generation_bulk', 'shops'));
        } elseif($request->coupon_type == Coupon::TYPE_CATEGORY_BASE) {
            $categories       = $this->categoryService->getAllList();
            $subCategories    = $this->subCateService->getList();
            $subSubCategories = $this->subSubCateService->getList();
            return view('partials.coupons.category_base_coupon', compact('categories', 'subCategories','subSubCategories','is_generation_bulk'));
        } else if ($request->coupon_type == Coupon::TYPE_CASH_BASE) {
            $shops = $this->shopService->getListForCoupon();
            return view('partials.coupons.cash_base_coupon', compact('is_generation_bulk', 'shops'));
        }
    }

    private function getDetailsForProductType($coupon) {
        $details = json_decode($coupon->details);
        if (!isset($details->info)) {
            $info = [];
            foreach ($details->products as $key => $productId) {
                $product = $this->productService->get($productId);
                $temp = [
                    'category_id'       => $product->category_id,
                    'subcategory_id'    => $product->subcategory_id,
                    'subsubcategory_id' => $product->subsubcategory_id,
                    'product_id'        => $productId
                ];
                $info[] = (object)$temp;
            }
            $details->info = $info;
        }
        return $details;
    }

    public function get_coupon_form_edit(Request $request)
    {
        if ($request->coupon_type == Coupon::TYPE_PRODUCT_BASE) {
            $categories = $this->categoryService->getAllList();
            $coupon = Coupon::findOrFail($request->id);
            $details = $this->getDetailsForProductType($coupon);
            return view('partials.coupons.product_base_coupon_edit', compact('categories', 'coupon', 'details'));
        } else if ($request->coupon_type == Coupon::TYPE_CART_BASE) {
            $coupon = Coupon::findOrFail($request->id);
            $shops = $this->shopService->getListForCoupon();
            $details = json_decode($coupon->details);
            return view('partials.coupons.cart_base_coupon_edit', compact('coupon', 'details', 'shops'));
        } elseif ($request->coupon_type == Coupon::TYPE_CATEGORY_BASE) {
            $categories = $this->categoryService->getAllList();
            $coupon           = Coupon::findOrFail($request->id);
            $details          = json_decode($coupon->details);
            $subCategories    = $this->subCateService->getList();
            $subSubCategories = $this->subSubCateService->getList();
            return view('partials.coupons.category_base_coupon_edit', compact('categories', 'coupon','details','subCategories','subSubCategories'));
        } else if ($request->coupon_type == Coupon::TYPE_CASH_BASE) {
            $coupon = Coupon::findOrFail($request->id);
            $details = json_decode($coupon->details);
            $shops = $this->shopService->getListForCoupon();
            return view('partials.coupons.cash_base_coupon_edit', compact('coupon', 'details', 'shops'));
        }
    }

    private function checkCouponCodeExists($couponCode) {
        try {
            $this->couponService->findBy('code', $couponCode);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function formatDataInputs($couponCode, $request, $key)
    {
        list($start, $end)  = explode(' - ', $request->daterange);
        if ($request->type_create) {
            if ($request->discount_type === Coupon::TYPE_PERCENTAGE) {
                $discount_type = $request->discount . '%';
            }
            $discount_type = '$'. $request->discount;
            $title = $request->coupon_code . ' voucher ' . $discount_type . ' max '. '$' . $request->max_coupon . ' key ' .$key;
            $inputs['title']      = $title;
        }
        
        $inputs['type']           = $request->coupon_type;
        $inputs['code']           = $couponCode;
        $inputs['free_shipping']  = $request->free_shipping??0;
        $inputs['discount']       = $request->discount;
        $inputs['seller_id']      = isset($request->seller_id) ? $request->seller_id : 0;
        $inputs['discount_type']  = $request->discount_type;
        $inputs['start_date']     = DateTime::createFromFormat('d/m/Y', $start)->format('Y-m-d');
        $inputs['end_date']       = DateTime::createFromFormat('d/m/Y', $end)->format('Y-m-d');
        $inputs['only_once']      = $request->only_once ?? 0;
        $inputs['published']      = $request->published ?? 0;

        $data = [];
        if ($request->coupon_type == Coupon::TYPE_PRODUCT_BASE) {
            $data['info'] = [];
            $data['products'] = [];
            for ($key = 0; $key < count($request->category_ids) - 1; $key++) {
                $details = [];
                $details['category_id']        = isset($request->category_ids[$key]) ? $request->category_ids[$key] : "";
                $details['subcategory_id']     = isset($request->subcategory_ids[$key]) ? $request->subcategory_ids[$key] : "";
                $details['subsubcategory_id']  = isset($request->subsubcategory_ids[$key]) ? $request->subsubcategory_ids[$key] : "";

                $details['product_id']         = $request->product_ids[$key];
                array_push($data['info'], $details);
                $data['products'][] = $request->product_ids[$key];

                $product = $this->productService->get($details['product_id']);
                if ($product->stocks()->count()) {
                    foreach($product->stocks as $variant) {
                        $data['products'][] = 'v-' . $variant->id;
                    }
                }
            }
            $data['min_buy'] = $request->min_buy;
            $data['max_coupon'] = $request->max_coupon;
            $data['max_discount'] = $request->max_discount;
        } else if ($request->coupon_type == Coupon::TYPE_CART_BASE) {
            $data['min_buy']        = $request->min_buy;
            $data['max_coupon']     = $request->max_coupon;
            $data['max_discount']   = $request->max_discount;
            $data['sellerIds']      = isset($request->seller_ids) ? $request->seller_ids : [];
        } else if ($request->coupon_type == Coupon::TYPE_CATEGORY_BASE) {
            $data['categoryIds']        = isset($request->category_ids) ? $request->category_ids : [];
            $data['subcategoryIds']     = isset($request->subcategory_ids) ? $request->subcategory_ids : [];
            $data['subsubcategoryIds']  = isset($request->subsubcategory_ids) ? $request->subsubcategory_ids : [];
            $data['min_buy']            = $request->min_buy;
            $data['max_coupon']         = $request->max_coupon;
            $data['max_discount']       = $request->max_discount;
        } else if ($request->coupon_type == Coupon::TYPE_CASH_BASE) {
            $data['min_buy']        = $request->min_buy;
            $data['max_coupon']     = $request->max_coupon;
            $data['sellerIds']      = isset($request->seller_ids) ? $request->seller_ids : [];
            $inputs['title']        = isset($title) ? $title : $request->coupon_name;
            $inputs['published']    = 0;
        }
        $inputs['details'] = $data;

        return $inputs;
    }

    public function updatePublished(Request $request)
    {
        $status = (boolean)$this->couponService->updatePublished($request->id);
        if ($status) {
            $response = [
                'status'  => 1,
                'message' => 'Published Coupon updated successfully !'
            ];
        } else {
            $response = [
                'status'  => 0,
                'message' => 'Something went wrong !'
            ];
        }
        return json_encode($response);
    }

    public function toggleActive(Request $request)
    {
        $status = (boolean)$this->couponService->toggleActive($request->id);
        if ($status) {
            $response = [
                'status'  => 1,
                'message' => 'Toggle Active Coupon successfully !'
            ];
        } else {
            $response = [
                'status'  => 0,
                'message' => 'Something went wrong !'
            ];
        }
        return json_encode($response);
    }

}

