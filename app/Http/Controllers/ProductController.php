<?php

namespace App\Http\Controllers;

use App\Language;
use App\Models\{Brand,
    Category,
    Product,
    ProductStock,
};
use App\Services\{BrandService,
    CategoryService,
    ShopService,
    ProductService,
    ProductStockService,
    ProductTranslationService
};
use Illuminate\Support\Facades\Auth;
use CoreComponentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ImageOptimizer;
use Session;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;
    protected $shopService;
    protected $productStockService;
    protected $productTranslateService;
    protected $categoryService;
    protected $brandService;

    public function __construct(
        ProductService $productService,
        ProductStockService $productStockService,
        ProductTranslationService $productTranslateService,
        CategoryService $categoryService,
        ShopService $shopService,
        BrandService $brandService
    ) {
        $this->productService               = $productService;
        $this->productStockService          = $productStockService;
        $this->productTranslateService      = $productTranslateService;
        $this->categoryService              = $categoryService;
        $this->shopService = $shopService;
        $this->brandService = $brandService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_products(Request $request)
    {
        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;
        $products = Product::where('added_by', 'admin');
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);

        return view('products.index', compact('products', 'type', 'col_name', 'query', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller_products(Request $request)
    {
        $col_name       = null;
        $query          = null;
        $seller_id      = null;
        $sort_search    = null;
        $products       = Product::where('added_by', 'seller');
        if ($request->has('user_id') && $request->user_id != null) {
            $products   = $products->where('user_id', $request->user_id);
            $seller_id  = $request->user_id;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%')
                ->orwhere('unit', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        if ($request->type != null) {
            $var        = explode(",", $request->type);
            $col_name   = $var[0];
            $query      = $var[1];
            $products   = $products->orderBy($col_name, $query);
            $sort_type  = $request->type;
        }
        $products = $products->orderBy('created_at', 'desc')->paginate(15);
        $type = 'Seller';

        return view('products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
        $product = new Product;
        $product->name = $request->name;
        $product->added_by = $request->added_by;
        if (Auth::user()->user_type == 'seller') {
            $product->user_id = Auth::user()->id;
        } else {
            $product->user_id = \App\User::where('user_type', 'admin')->first()->id;
        }
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->subsubcategory_id = $request->subsubcategory_id;
        $product->brand_id = $request->brand_id;
        $product->current_stock = $request->current_stock;
        $product->barcode = $request->barcode;
        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            } else {
                $product->refundable = 0;
            }
        }
        $photos = array();
        if ($request->hasFile('photos')) {
            foreach ($request->photos as $key => $photo) {
                $path = $photo->store('uploads/products/photos');
                array_push($photos, $path);
                //ImageOptimizer::optimize(base_path('public/').$path);
            }
            $product->photos = json_encode($photos);
        }
        if ($request->hasFile('thumbnail_img')) {
            $product->thumbnail_img = $request->thumbnail_img->store('uploads/products/thumbnail');
            //ImageOptimizer::optimize(base_path('public/').$product->thumbnail_img);
        }
        $product->unit = $request->unit;
        $product->min_qty = $request->min_qty;
        $product->tags = implode('|', $request->tags);
        $product->description = $request->description;
        $product->video_provider = $request->video_provider;
        $product->video_link = $request->video_link;
        $product->unit_price = $request->unit_price;
        $product->purchase_price = $request->purchase_price;
        $product->tax = $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount;
        $product->discount_type = $request->discount_type;
        $product->shipping_type = $request->shipping_type;
        if ($request->has('shipping_type')) {
            if ($request->shipping_type == 'free') {
                $product->shipping_cost = 0;
            } elseif ($request->shipping_type == 'flat_rate') {
                $product->shipping_cost = $request->flat_shipping_cost;
            }
        }
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        if ($request->hasFile('meta_img')) {
            $product->meta_img = $request->meta_img->store('uploads/products/meta');
            //ImageOptimizer::optimize(base_path('public/').$product->meta_img);
        } else {
            $product->meta_img = $product->thumbnail_img;
        }
        if ($product->meta_title == null) {
            $product->meta_title = $product->name;
        }
        if ($product->meta_description == null) {
            $product->meta_description = $product->description;
        }
        if ($request->hasFile('pdf')) {
            $product->pdf = $request->pdf->store('uploads/products/pdf');
        }
        $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
//        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
//            $product->colors = json_encode($request->colors);
//        } else {
//            $colors = array();
//            $product->colors = json_encode($colors);
//        }
//        $choice_options = array();
//        if ($request->has('choice_no')) {
//            foreach ($request->choice_no as $key => $no) {
//                $str = 'choice_options_' . $no;
//                $item['attribute_id'] = $no;
//                $item['values'] = explode(',', implode('|', $request[$str]));
//                array_push($choice_options, $item);
//            }
//        }
//        if (!empty($request->choice_no)) {
//            $product->attributes = json_encode($request->choice_no);
//        } else {
//            $product->attributes = json_encode(array());
//        }
//        $product->choice_options = json_encode($choice_options);
        //$variations = array();
        $product->save();
        //combinations start
//        $options = array();
//        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
//            $colors_active = 1;
//            array_push($options, $request->colors);
//        }
//        if ($request->has('choice_no')) {
//            foreach ($request->choice_no as $key => $no) {
//                $name = 'choice_options_' . $no;
//                $my_str = implode('|', $request[$name]);
//                array_push($options, explode(',', $my_str));
//            }
//        }
//        //Generates the combinations of customer choice options
//        $combinations = combinations($options);
//        if (count($combinations[0]) > 0) {
//            $product->variant_product = 1;
//            foreach ($combinations as $combinationKey => $combination) {
//                $str = '';
//                foreach ($combination as $itemKey => $item) {
//                    if ($itemKey > 0) {
//                        $str .= ProductStock::SPLIT_SYMBOL . str_replace(' ', '', $item);
//                    } else {
//                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
//                            $color_name = \App\Color::where('code', $item)->first()->name;
//                            $str .= $color_name;
//                        } else {
//                            $str .= str_replace(' ', '', $item);
//                        }
//                    }
//                }
//                // $item = array();
//                // $item['price'] = $request['price_'.str_replace('.', '_', $str)];
//                // $item['sku'] = $request['sku_'.str_replace('.', '_', $str)];
//                // $item['qty'] = $request['qty_'.str_replace('.', '_', $str)];
//                // $variations[$str] = $item;
//                $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $str)->first();
//                if ($product_stock == null) {
//                    $product_stock = new ProductStock;
//                    $product_stock->product_id = $product->id;
//                }
//                $product_stock->variant = $str;
//                $product_stock->price = $request['price_' . str_replace('.', '_', $str)];
//                $product_stock->sku = $request['sku_' . str_replace('.', '_', $str)];
//                $product_stock->qty = $request['qty_' . str_replace('.', '_', $str)];
//                $product_stock->save();
//            }
//        }
        //combinations end
        foreach (Language::all() as $key => $language) {
            $data = openJSONFile($language->code);
            $data[$product->name] = $product->name;
            saveJSONFile($language->code, $data);
        }
        $product->save();
        flash(__('Product has been inserted successfully'))->success();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return redirect()->route('products.admin');
        } else {
            if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                $seller = Auth::user()->seller;
                $seller->remaining_uploads -= 1;
                $seller->save();
            }

            return redirect()->route('seller.products');
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
    public function admin_product_edit($id)
    {
        $product = Product::findOrFail(decrypt($id));
        $tags = json_decode($product->tags);
        $categories = Category::all();

        return view('products.edit', compact('product', 'categories', 'tags'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit($id)
    {

        $product    = $this->productService->get(decrypt($id));
        $tags       = json_decode($product->tags);
        $brands     = $this->brandService->getAllWithConditions(['status' => Brand::STATUS_ACTIVE]);
        $categories = $this->categoryService->getAllList();
        return view('products.edit', compact('product', 'categories', 'tags', 'brands'));
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
        DB::beginTransaction();
        try {
            $product = $this->productService->get($id);
            if (!$product) return abort(404);

            $productInputs              = $this->productService->formatDataInputs($request, $product);
//            $productStockInputs         = $this->productStockService->formatDataInputs($request, $product);
//
//
//            $product->stocks()->delete();
//            $productStockResult         = $this->productStockService->createVariants($productStockInputs);

            if ($productInputs) {
                $productTranslateInputs = $this->productTranslateService->formatDataInputs($request, $product->id);
                $this->productTranslateService->updateOrCreateMultiple($productTranslateInputs);
                Log::info('Product translation is updated');
                $this->productService->update($id, $productInputs);
                Log::info('Product is updated');
                
                DB::commit();
                Log::info('DB is commited');
                flash(__('Product has been updated successfully'))->success();

                DB::table('logging')->insert([
                    'user_id'       => Auth::user()->id,
                    'action'        => 'update product details id -' . $product->id,
                    'ip_address'    => $request->ip()
                ]);

                if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
                    return redirect()->route('products.seller');
                } else {
                    return redirect()->route('seller.products');
                }
            } else {
                flash(__('Something went wrong'))->error();
                return back();
            }
        } catch (Exception $e) {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        //lets log//
        DB::table('logging')->insert([
            'user_id' => Auth::user()->id,
            'action' => 'delete product id -' . $product->id,
            'ip_address' => $request->ip()
        ]);
        //
        $product->unit = "deleted-" . $product->unit;
        $product->published = 0;
        $product->save();
        flash(__('Product has been deleted successfully'))->success();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return redirect()->route('products.seller');
        } else {
            return redirect()->route('seller.products');
        }
        /**
         * if(Product::destroy($id)){
         * foreach (Language::all() as $key => $language) {
         * $data = openJSONFile($language->code);
         * unset($data[$product->name]);
         * saveJSONFile($language->code, $data);
         * }
         * flash(__('Product has been deleted successfully'))->success();
         * if(Auth::user()->user_type == 'admin'){
         * return redirect()->route('products.admin');
         * }
         * else{
         * return redirect()->route('seller.products');
         * }
         * }
         * else{
         * flash(__('Something went wrong'))->error();
         * return back();
         * }
         **/
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id)
    {
        $product = Product::find($id);
        $product_new = $product->replicate();
        $product_new->slug = substr($product_new->slug, 0, -5) . Str::random(5);
        if ($product_new->save()) {
            flash(__('Product has been duplicated successfully'))->success();
            if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
                return redirect()->route('products.admin');
            } else {
                return redirect()->route('seller.products');
            }
        } else {
            flash(__('Something went wrong'))->error();

            return back();
        }
    }

    public function get_products_by_subcategory(Request $request)
    {
        $products = Product::where('subcategory_id', $request->subcategory_id)
            ->where('published', 1)
            ->get();

        return $products;
    }

    public function get_products_by_subsubcategory(Request $request)
    {
        $products = Product::where('subsubcategory_id', $request->subsubcategory_id)
            ->where('published', 1)
            ->get();

        return $products;
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)
            ->where('published', 1)
            ->get();

        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        if ($product->save()) {
            return 1;
        }

        return 0;
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;
        if ($product->added_by == 'seller' && \App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            $seller = $product->user->seller;
            if ($seller->invalid_at != null && Carbon::now()->diffInDays(Carbon::parse($seller->invalid_at), false) <= 0) {
                return 0;
            }
        }
        //lets log//
        DB::table('logging')->insert([
            'user_id' => Auth::user()->id,
            'action' => 'update product published id -' . $product->id . ' to ' . $request->status,
            'ip_address' => $request->ip()
        ]);
        //
        $product->save();

        return 1;
    }

    public function updateFeatured(\App\Models\Product $product, Request $request)
    {
        $shop = $product->user->shop;

        if ($request->status) { // add to featured
            $this->shopService->addFeaturedProduct($shop, [$product->id]);
        } else {
            $this->shopService->removeFeaturedProduct($shop, $product->id);
        }
        //lets log//
        DB::table('logging')->insert([
            'user_id' => Auth::user()->id,
            'action' => 'update product featured id -' . $product->id . ' to ' . $request->status,
            'ip_address' => $request->ip()
        ]);
        if ($product->save()) {
            return 1;
        }

        return 1;
    }

    public function updateOnlyMicrosite(Request $request)
    {
        $data = ['only_microsite' => $request->status];

        DB::table('logging')->insert([
            'user_id' => Auth::user()->id,
            'action' => 'update product only microsite id -' . $request->id . ' to ' . $request->status,
            'ip_address' => $request->ip()
        ]);

        $this->productService->update($request->id, $data);
        return 1;
    }

    public function sku_combination(Request $request)
    {
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }
        $unit_price = $request->unit_price;
        $product_name = $request->name;
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        $combinations = combinations($options);

        return view('partials.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }
        $product_name = $request->name[0];
        $unit_price = $request->unit_price;
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        $combinations = combinations($options);

        return view('partials.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }
}
