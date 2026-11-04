<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Models\Category;
use App\Models\SubSubCategory;
use App\Services\SubSubCategoryService;
use App\Services\SubSubCategoryTranslationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CategoryService;
use App\Services\BrandService;

class SubSubCategoryController extends Controller
{
    /**
     * @param SubSubCategoryService
     */
    protected $subSubCategoryService;

    /**
     * @param SubSubCategoryTranslationService
     */
    protected $subSubCategoryTranslationService;

    /**
     * @param CategoryService
     */
    protected $categoryService;

    /**
     * @param BrandService
     */
    protected $brandService;

    public function __construct(
        SubSubCategoryService $subSubCategoryService,
        SubSubCategoryTranslationService $subSubCategoryTranslationService,
        CategoryService $categoryService,
        BrandService $brandService
    ) {
        $this->subSubCategoryService               = $subSubCategoryService;
        $this->subSubCategoryTranslationService    = $subSubCategoryTranslationService;
        $this->categoryService                     = $categoryService;
        $this->brandService                        = $brandService;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search        = $request->has('search') ? $request->search : null;
        $subSubCategories   = $this->subSubCategoryService->filterData($request);
        return view('subsubcategories.index', compact('subSubCategories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->categoryService->getAll();
        $brands     = $this->brandService->getAll();
        return view('subsubcategories.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $inputs             = $this->subSubCategoryService->formatDataInputs($request);
            $subSubCategory     = $this->subSubCategoryService->create($inputs);
            if ($subSubCategory) {
                $inputSubSubCategoryTranslations        = $this->subSubCategoryTranslationService->formatDataInputs($request, $subSubCategory->id);
                $this->subSubCategoryTranslationService->insert($inputSubSubCategoryTranslations);
                DB::commit();
                flash(__('SubSubCategory has been inserted successfully'))->success();
                return redirect()->route('subsubcategories.index');
            } else {
                flash(__('Something went wrong'))->error();
                return back();
            }
        } catch (Exception $e) {
            DB::rollBack();
            flash(__('Something went wrong'))->error();
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
        $subSubCategory     = $this->subSubCategoryService->get(decrypt($id));
        $categories         = $this->categoryService->getAll();
        $brands             = $this->brandService->getAll();
        return view('subsubcategories.edit', compact('subSubCategory', 'categories', 'brands'));
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
        try {
            DB::beginTransaction();
            $subSubCategory = $this->subSubCategoryService->get($id);

            $inputs = $this->subSubCategoryService->formatDataInputs($request, $subSubCategory);
            $result = $this->subSubCategoryService->update($id, $inputs);

            if ($result) {
                $inputSubSubCategoryTranslations  = $this->subSubCategoryTranslationService->formatDataInputs($request, $subSubCategory->id);
                $this->subSubCategoryTranslationService->updateOrCreateMultiple($inputSubSubCategoryTranslations);
                DB::commit();
                flash(__('SubSubCategory has been updated successfully'))->success();
                return redirect()->route('subsubcategories.index');
            } else {
                flash(__('Something went wrong'))->error();
                return back();
            }
        } catch (Exception $e) {
            DB::rollBack();
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $subSubCategory = $this->subSubCategoryService->get($id);
            if (!isset($subSubCategory)) return abort(404);

            $products = $subSubCategory->products;
            if (isset($products) && count($products) > 0) {
                flash(__('SubSubCategory contains products, cannot be deleted'))->error();
                return back();
            }
            if ($this->subSubCategoryService->destroy($id)) {
                DB::commit();
                flash(__('SubSubCategory has been deleted successfully'))->success();
                return redirect()->route('subsubcategories.index');
            } else {
                flash(__('Something went wrong'))->error();
                return back();
            }
        } catch (Exception $e) {
            DB::rollBack();
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function get_subsubcategories_by_subcategory(Request $request)
    {
        $subsubcategories = $this->subSubCategoryService->getAllWithConditions([
            'sub_category_id' => $request->subcategory_id, 
            'status' => SubSubCategory::STATUS_ACTIVE
        ]);

        return $subsubcategories;
    }

    public function updateStatus(Request $request)
    {
        $status = (boolean)$this->subSubCategoryService->updateStatus($request->id);
        if ($status) {
            $response = [
                'status'  => 1,
                'message' => 'Published Sub Subcategory updated successfully!'
            ];
        } else {
            $response = [
                'status'  => 0,
                'message' => 'Something went wrong!'
            ];
        }
        return json_encode($response);
    }



    // public function get_brands_by_subsubcategory(Request $request)
    // {
    //     $brand_ids = json_decode(SubSubCategory::findOrFail($request->subsubcategory_id)->brands);
    //     $brands = array();
    //     foreach ($brand_ids as $key => $brand_id) {
    //         array_push($brands, Brand::findOrFail($brand_id));
    //     }
    //     return $brands;
    // }
    // public function get_attributes_by_subsubcategory(Request $request)
    // {
    //     $attribute_ids = json_decode(SubSubCategory::findOrFail($request->subsubcategory_id)->attributes);
    //     $attributes = array();
    //     foreach ($attribute_ids as $key => $attribute_id) {
    //         if(\App\Attribute::find($attribute_id) != null){
    //             array_push($attributes, \App\Attribute::findOrFail($attribute_id));
    //         }
    //     }
    //     return $attributes;
    // }
}
