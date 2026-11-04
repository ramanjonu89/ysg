<?php
namespace App\Http\Controllers;

use App\Models\{BusinessSetting,
    Shop
};
use App\Services\{SellerService,
    ShopService,
    UserOtpService,
    UserService
};
use App\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    /**
     * @param ShopService
     */
    protected $shopService;

    /**
     * @param SellerService
     */
    protected $sellerService;

    /**
     * @param UserOtpService
     */
    protected $userOtpService;

    /**
     * @param UserService
     */
    protected $userService;

    public function __construct(
        ShopService    $shopService,
        SellerService  $sellerService,
        UserOtpService $userOtpService,
        UserService    $userService
        
    ) {
        $this->shopService    = $shopService;
        $this->sellerService  = $sellerService;
        $this->userOtpService = $userOtpService;
        $this->userService    = $userService;
        $this->middleware('user', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->shop;

        return view('frontend.seller.shop', compact('shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::check() && Auth::user()->user_type == 'admin') {
            flash(__('Admin can not be a seller'))->error();

            return back();
        } else {
            $user_type = User::TYPE_SELLER; 
            return view('frontend.seller_form',compact('user_type'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sellerDataAuth = $this->userOtpService->getFirstWithConditions(['original_otp' => $request->otp, 'email' => $request->email, 'user_type' => User::TYPE_SELLER]);
        if (!$sellerDataAuth) {
            flash(__('The OTP is not valid yet!'))->error();
            return back();
        }
        if (!$sellerDataAuth->isExpired()) {
            flash(__('The OTP has expired!'))->error();
            return redirect()->route('be.a.seller');
        }

        $data = $this->userOtpService->formatDataInputsRegister($sellerDataAuth);
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->customer != null) {
                $user->customer->delete();
            }
        } else if ($data['password'] !== $data['password_confirmation']) {
            flash(__('Sorry! Password did not match.'))->error();
            return back();
        } else {
            $dataUser['name'] = $data['name'];
            $dataUser['email'] = $data['email'];
            $dataUser['user_type'] = User::TYPE_CUSTOMER;
            $dataUser['password'] = Hash::make($data['password']);
            $user = $this->userService->create($dataUser);
        }

        Log::info('========== CREATE SELLER ==========');
        Log::info('SELLER NAME: ' . $data['name']);
        Log::info(request()->ip());
        Log::info(request()->userAgent());

        $shopExists = $this->shopService->getFirstWithConditions(['user_id' => $user->id]);
        if ($shopExists) {
            flash(__('Sorry! The Shop was added using this account.'))->error();
            return back();
        }
        $dataShop['user_id'] = $user->id;
        $dataShop['name']    = $data['name'];
        $shop = $this->shopService->create($dataShop);

        if (!$shop) {
            flash(__('Sorry! Something went wrong.'))->error();
            return back();
        }

        // update slug shop
        $dataUpdateShop['slug'] = preg_replace('/\s+/', '-', $data['name']) . '-' . $shop->id;
        $this->shopService->update($shop->id, $dataUpdateShop);

        // update type customer
        $dataUpdateUser['user_type'] = User::TYPE_SELLER;
        $this->userService->update($user->id, $dataUpdateUser);

        // insert seller
        $dataSeller['user_id'] = $user->id;
        $this->sellerService->create($dataSeller);

        auth()->login($user, false);
        if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();
        } else {
            //$user->notify(new EmailVerificationNotification());
        }
        try {
            $this->shopService->sendMailToManagerSellerRegistration($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        if (isset($shop)) {
            $this->userOtpService->destroy($sellerDataAuth->id);
        }

        auth()->logout();
        flash(__('Our CS team will contact you shortly!'))->success();
        return redirect()->route('home');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::find($id);
        if ($request->has('name') && $request->has('address')) {
            $shop->name = $request->name;
            if ($request->has('shipping_cost')) {
                $shop->shipping_cost = $request->shipping_cost;
            }
            if ($request->has('shipping_cost_above')) {
                $shop->shipping_cost_above = $request->shipping_cost_above;
            }
            $shop->address = $request->address;
            $shop->order_type = $request->order_type;
            $shop->slug = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;
            $shop->meta_title = $request->meta_title;
            $shop->meta_description = $request->meta_description;
            if ($request->hasFile('logo')) {
                $shop->logo = $request->logo->store('uploads/shop/logo');
            }
            if ($request->has('pick_up_point_id')) {
                $shop->pick_up_point_id = json_encode($request->pick_up_point_id);
            } else {
                $shop->pick_up_point_id = json_encode(array());
            }
        }
        if ($request->has('facebook') || $request->has('google') || $request->has('twitter') || $request->has('youtube') || $request->has('instagram')) {
            $shop->facebook = $request->facebook;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
            $shop->instagram = $request->instagram;
            $shop->linkedin = $request->linkedin;
        } 

        $shop->featured_products = $request->featured_products;

        if ($shop->save()) {
            flash(__('Your Shop has been updated successfully!'))->success();

            return back();
        }
        flash(__('Sorry! Something went wrong.'))->error();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function verify_form(Request $request)
    {
        if (Auth::user()->seller->verification_info == null) {
            $shop = Auth::user()->shop;

            return view('frontend.seller.verify_form', compact('shop'));
        } else {
            flash(__('Sorry! You have sent verification request already.'))->error();

            return back();
        }
    }

    public function verify_form_store(Request $request)
    {
        $data = array();
        $i = 0;
        foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
            $item = array();
            if ($element->type == 'text') {
                $item['type'] = 'text';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'select' || $element->type == 'radio') {
                $item['type'] = 'select';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'multi_select') {
                $item['type'] = 'multi_select';
                $item['label'] = $element->label;
                $item['value'] = json_encode($request['element_' . $i]);
            } elseif ($element->type == 'file') {
                $item['type'] = 'file';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i]->store('uploads/verification_form');
            }
            array_push($data, $item);
            $i++;
        }
        $seller = Auth::user()->seller;
        $seller->verification_info = json_encode($data);
        if ($seller->save()) {
            flash(__('Your shop verification request has been submitted successfully!'))->success();

            return redirect()->route('dashboard');
        }
        flash(__('Sorry! Something went wrong.'))->error();

        return back();
    }
}