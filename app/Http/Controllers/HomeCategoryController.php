<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\HomeCategory;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Services\{ AttributeService,
    CategoryService,
    CorporateAccessService,
    MicrositeService,
    ProductService,
    SellerService};
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeCategoryController extends Controller
{
    /**
     * @param AttributeService
     */
    protected $attributeService;

    /**
     * @param MicrositeService;
     */
    protected $micrositeService;

    /**
     * @param ProductService
     */
    protected $productService;

    /**
     * @param SellerService
     */
    protected $sellerService;

    /**
     * @param CategoryService
     */
    protected $categoryService;

    public function __construct(
        
        AttributeService $attributeService,
        MicrositeService $micrositeService,
        ProductService   $productService,
        SellerService    $sellerService,
        CategoryService  $categoryService
    ) {
        $this->attributeService = $attributeService;
        $this->micrositeService = $micrositeService;
        $this->productService   = $productService;
        $this->sellerService    = $sellerService;
        $this->categoryService  = $categoryService;
    }

    public function index()
    {
        $home_categories = HomeCategory::all();

        return view('home_categories.index', compact('home_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->categoryService->getMainCategories();
        return view('home_categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $home_category = new HomeCategory;
        $home_category->category_id = $request->category_id;
        $home_category->subsubcategories = json_encode($request->subsubcategories);
        if ($home_category->save()) {
            flash(__('Home Page Category has been inserted successfully'))->success();

            return redirect()->route('frontend_settings.home.index');
        }
        flash(__('Something went wrong'))->error();

        return back();
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
        $homeCategory = HomeCategory::findOrFail($id);

        return view('home_categories.edit', compact('homeCategory'));
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
        $home_category = HomeCategory::findOrFail($id);
        $home_category->category_id = $request->category_id;
        $home_category->subsubcategories = json_encode($request->subsubcategories);
        if ($home_category->save()) {
            flash(__('Home Page Category has been inserted successfully'))->success();

            return redirect()->route('frontend_settings.home.index');
        }
        flash(__('Something went wrong'))->error();

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
        if (HomeCategory::destroy($id)) {
            flash(__('Home Page Category has been deleted successfully'))->success();

            return redirect()->route('frontend_settings.home.index');
        } else {
            flash(__('Something went wrong'))->error();

            return back();
        }
    }

    public function update_status(Request $request)
    {
        $home_category = HomeCategory::findOrFail($request->id);
        $home_category->status = $request->status;
        if ($home_category->save()) {
            return 1;
        }

        return 0;
    }

    public function getSubSubCategories(Request $request)
    {
        $result = array();
        $subcategories = Category::find($request->category_id)->subcategories;
        foreach ($subcategories as $key => $subcategory) {
            foreach ($subcategory->subSubcategories as $key => $subsubcategory) {
                $subsubcategory['number_of_products'] = count($subsubcategory->products);
                array_push($result, $subsubcategory);
            }
        }

        return $result;
    }

    // category show here
    public function viewCategory(Request $request){
        $CAService              = app()->make(CorporateAccessService::class);
        $canViewCSSPage         = auth()->user() ? $CAService->canViewCSSPage(auth()->user()) : false;
        $query                  = $request->q;
        $sort_by                = $request->get('sort_by', '');
        $rate                   = $request->get('rate', '');
        $discount               = $request->get('discount', '');
        $storeTypes             = $request->get('store_types', null);
        $min_price              = $request->get('min_price') ?? 0;
        $max_price              = $request->get('max_price') ?? 9999;
        $category               = Category::where('slug', $request->category_slug)->first();
        $subCategory            = SubCategory::where('slug', $request->subcategory_slug)->first();
        $subSubCategory         = SubSubCategory::where('slug', $request->subsubcategory_slug)->first();
        $category_id            = $category ? array($category->id) : null;
        $subcategory_id         = $subCategory ? array($subCategory->id) : null;
        $subsubcategory_id      = $subSubCategory ? array($subSubCategory->id) : null;
        $selectedPageSize       = isset($request->pageSize) ? $request->pageSize : 12;
        $pageSizeOptions        = [12, 24, 36, 48];
        $conditions             = ['published' => 1];
        if ($category_id != null) {
            $conditions = array_merge($conditions, ['category_id' => $category_id]);
        }
        if(isset($request->brand_ids)){
            $conditions = array_merge($conditions, ['brand_id' => $request->brand_ids]);
        }
        if ($subcategory_id != null) {
            $conditions = array_merge($conditions, ['subcategory_id' => $subcategory_id]);
        }
        if ($subsubcategory_id != null) {
            $conditions = array_merge($conditions, ['subsubcategory_id' => $subsubcategory_id]);
        }

        $finalMinPrice      = $min_price ?? 0;	
        $finalMaxPrice      = $max_price ?? 99999;
        $brandIds  = isset($request->brand_ids)  ? $request->brand_ids : [];
        $sellerIds = isset($request->seller_ids) ? $request->seller_ids : [];

        $micrositeUserIds   = $this->micrositeService->getMicrositeUserIds();

        $companyTypes[]     = 1;
        // filter css
        if($canViewCSSPage === true && isset($request->store_types) && !empty($request->store_types)){
            $companyTypes = [];
            if(isset($request->store_types[2]) && $request->store_types[2] == User::COMPANY_TYPE_PRIVATE_EVENT) $companyTypes[] = 2;
            if(isset($request->store_types[1]) && $request->store_types[1] == User::COMPANY_TYPE_FLAG_SHIP) $companyTypes[] = 1;
        }

        $userIds      = $this->sellerService->getUserIdQuery($sellerIds, $companyTypes, $micrositeUserIds);
        $productQuery = $this->productService->getListProductByCategory($query, $userIds, $conditions, $finalMinPrice, $finalMaxPrice);

        if (!empty($query)) {
            $searchController = new SearchController;
            $searchController->store($request);
        }
        $tmp                            = clone $productQuery;
        $tmp2                           = clone $productQuery;
        list($attributes, $all_colors)  = $this->get_attribute_and_color_list_for_filter($tmp);
        $selected_attributes            = [];
        $selectedAttributeValues        = [];
        foreach ($attributes as $key => $attribute) {
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                    $selectedAttributeValues[] = '"' . $value . '"';
                }
                $selected_attributes[$attribute['id']] = $request['attribute_' . $attribute['id']];
            }
        }
        if (!empty($selectedAttributeValues)) {
            $productQuery = $this->productService->filterByAttributeValues($productQuery, $selectedAttributeValues);
        }
        $selected_color = isset($request->color) ? $request->color : null;
        $queryResults = $this->productService->filterBySubConditions($productQuery, $selected_color, $rate, $discount, $sort_by);

        $products = $queryResults->paginate($selectedPageSize)->appends(request()->query());

        $validProducts = DB::table('products')
            ->leftJoin('users', 'products.user_id', 'users.id')
            ->leftJoin('sellers', 'users.id', 'sellers.user_id')
            ->where(function ($q) use ($category_id, $subcategory_id, $subsubcategory_id) {
                if ($category_id) {
                    $q->where('products.category_id', '=', $category_id);
                }
                if ($subcategory_id) {
                    $q->where('products.subcategory_id', '=', $subcategory_id);
                }
                if ($subsubcategory_id) {
                    $q->where('products.subsubcategory_id', '=', $subsubcategory_id);
                }
            })
            ->where('users.company_type', 1)
            ->where('sellers.verification_status', 1);
        $validBrandIds = $tmp2
            ->whereNotNull('products.brand_id')
            ->distinct()
            ->pluck('products.brand_id')
            ->toArray();
        $validUserIds = $tmp2
            ->distinct()
            ->pluck('products.user_id')
            ->toArray();
        if (isMicrosite()) {
            $validUserIds = [getMicrositeShop()->user->id];
        }
        $brands = DB::table('brands')
            ->whereIn('id', $validBrandIds)
            ->select(['brands.id', 'brands.name'])
            ->get()
            ->toArray();
        $sellers = DB::table('users')
            ->leftJoin('sellers', 'users.id', 'sellers.user_id')
            ->whereIn('users.id', $validUserIds)
            ->select(['sellers.id', 'users.name'])
            ->orderBy('users.name')
            ->get();
        $existing_attributes = $this->attributeService->getAttribute();

        return view(
            'frontend.category.product_listing',
            compact(
                'products',
                'query',
                'category_id',
                'subcategory_id',
                'subsubcategory_id',
                'brandIds',
                'sort_by',
                'sellerIds',
                'min_price',
                'max_price',
                'attributes',
                'selected_attributes',
                'all_colors',
                'selected_color',
                'brands',
                'sellers',
                'existing_attributes',
                'pageSizeOptions',
                'selectedPageSize',
                'category',
                'subCategory',
                'subSubCategory',
                'rate',
                'discount',
                'storeTypes',
                'canViewCSSPage'
            )
        );
    }

    public function get_attribute_and_color_list_for_filter($productQuery)
    {
        $non_paginate_products = $productQuery
            ->where('colors', '!=', '[]')
            ->orWhere('attributes', '!=', '[]')
            ->get();
        $attributes = [];
        $all_colors = [];
        foreach ($non_paginate_products as $product) {
            if ($product->attributes != '[]') {
                $currentAttributes = json_decode($product->attributes);
                foreach ($currentAttributes as $attributeId) {
                    if (!array_key_exists($attributeId, $attributes)) {
                        $attributes[$attributeId]['id'] = $attributeId;
                        $attributes[$attributeId]['values'] = [];
                    }
                    $currentOptions = json_decode($product->choice_options);
                    foreach ($currentOptions as $choice_option) {
                        if ($choice_option->attribute_id == $attributeId) {
                            foreach ($choice_option->values as $choice) {
                                if (!in_array($choice, $attributes[$attributeId]['values'])) {
                                    $attributes[$attributeId]['values'][] = $choice;
                                }
                            }
                        }
                    }
                }
            }
            if ($product->colors != '[]') {
                foreach (json_decode($product->colors) as $color) {
                    if (!in_array($color, $all_colors)) {
                        $all_colors[] = $color;
                    }
                }
            }
        }

        return [$attributes, $all_colors];
    }

}

