<?php
namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'order_type' => 'required',
        ]);

        $validator->after(function ($validator) use ($request, $shop) {
            if (
                $request->has('order_type')
                && $this->includedSelfCollection($request->order_type)
                && $shop->user->addresses->count() == 0
            ) {
                $validator->errors()->add('address_required', __('You need at least one address to enable self collection!'));
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $shop->name = $request->name;
        if ($request->has('shipping_cost')) {
            $shop->shipping_cost = $request->shipping_cost;
        }
        if ($request->has('shipping_cost_above')) {
            $shop->shipping_cost_above = $request->shipping_cost_above;
        }
        /**
         * 1 - Delivery
         * 2 - Self Collection
         * 3 - Both (1 + 2)
         *  */       
        $shop->order_type = array_sum($request->order_type);
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
        if ($request->has('facebook') || $request->has('google') || $request->has('twitter') || $request->has('youtube') || $request->has('instagram')) {
            $shop->facebook = $request->facebook;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
            $shop->instagram = $request->instagram;
            $shop->linkedin = $request->linkedin;
        }

        $shop->featured_products = $request->featured_products;
        
        if($request->allowed_attributes !== null) {
            $shop->allowedAttributes()->sync($request->allowed_attributes);
        }
        if ($shop->save()) {
            flash(translate('Shop configs has been updated successfully!'))->success();

            return back();
        }
        flash(translate('Sorry! Something went wrong.'))->error();

        return back();
    }

    public function includedSelfCollection($orderType)
    {
        return in_array(Shop::ORDER_TYPE_SELF_COLLECTION, $orderType) || in_array(Shop::ORDER_TYPE_BOTH, $orderType);
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
}