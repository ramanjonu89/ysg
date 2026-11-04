<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\SubCategoryService;
use App\Services\SubCategoryTranslationService;
use Exception;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
    /**
     * @param SubCategoryService
     */
    protected $subCategoryService;

    /**
     * @param SubCategoryTranslationService
     */
    protected $subCategoryTranslationService;

    /**
     * @param CategoryService
     */
    protected $categoryService;

    public function __construct(
        SubCategoryService $subCategoryService,
        SubCategoryTranslationService $subCategoryTranslationService,
        CategoryService $categoryService
    ) {
        $this->subCategoryService               = $subCategoryService;
        $this->subCategoryTranslationService    = $subCategoryTranslationService;
        $this->categoryService                  = $categoryService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search    = $request->has('search') ? $request->search : null;
        $subcategories  = $this->subCategoryService->filterData($request);
        return view('subcategories.index', compact('subcategories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->categoryService->getAll();
        return view('subcategories.create', compact('categories'));
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
            $inputs         = $this->subCategoryService->formatDataInputs($request);
            $subCategory    = $this->subCategoryService->create($inputs);
            if ($subCategory) {
                // store category translation
                $inputSubCategoryTranslations  = $this->subCategoryTranslationService->formatDataInputs($request, $subCategory->id);
                $this->subCategoryTranslationService->insert($inputSubCategoryTranslations);
                DB::commit();
                flash(__('Subcategory has been inserted successfully'))->success();
                return redirect()->route('subcategories.index');
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
        $subcategory    = $this->subCategoryService->get(decrypt($id));
        $categories     = $this->categoryService->getAll();
        return view('subcategories.edit', compact('categories', 'subcategory'));
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
            $subCategory    = $this->subCategoryService->get($id);
            $inputs         = $this->subCategoryService->formatDataInputs($request, $subCategory);
            $result         = $this->subCategoryService->update($id, $inputs);

            if ($result) {
                $inputSubCategoryTranslations  = $this->subCategoryTranslationService->formatDataInputs($request, $subCategory->id);
                $this->subCategoryTranslationService->updateOrCreateMultiple($inputSubCategoryTranslations);

                DB::commit();
                flash(__('Subcategory has been updated successfully'))->success();
                return redirect()->route('subcategories.index');
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

            $subCategory    = $this->subCategoryService->get($id);
            $products       = $subCategory->products;

            if (isset($products) && count($products) > 0) {
                flash(__('Sub Category contains products, cannot be deleted'))->error();
                return back();
            }

            if ($this->subCategoryService->destroy($id)) {
                DB::commit();
                flash(__('Subcategory has been deleted successfully'))->success();
                return redirect()->route('subcategories.index');
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

    public function get_subcategories_by_category(Request $request)
    {
        $subcategories = $this->subCategoryService->getAllWithConditions([
            'category_id' => $request->category_id,
            'status' => SubCategory::STATUS_ACTIVE
        ]);

        return $subcategories;
    }

    public function updateStatus(Request $request)
    {
        $status = (boolean)$this->subCategoryService->updateStatus($request->id);
        if ($status) {
            $response = [
                'status'  => 1,
                'message' => 'Published Subcategory updated successfully!'
            ];
        } else {
            $response = [
                'status'  => 0,
                'message' => 'Something went wrong!'
            ];
        }
        return json_encode($response);
    }

}
