<?php
namespace App\Http\Controllers;

use App\Excel\CategoryExport;
use App\Services\CategoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\CategoryTranslationService;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{

    /**
     * @param CategoryService
     */
    protected $categoryService;

     /**
     * @param CategoryTranslationService
     */
    protected $categoryTranslationService;

    public function __construct( CategoryService $categoryService, CategoryTranslationService $categoryTranslationService)
    {
        $this->categoryService = $categoryService;
        $this->categoryTranslationService = $categoryTranslationService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search    = $request->has('search') ? $request->search : null;
        $categories     = $this->categoryService->filterData($request);
        return view('categories.index', compact('categories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
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
            $inputs     = $this->categoryService->formatDataInputs($request);
            $category   = $this->categoryService->create($inputs);

            if ($category) {
                // store category translation
                $inputCategoryTranslations  = $this->categoryTranslationService->formatDataInputs($request, $category->id);
                $this->categoryTranslationService->insert($inputCategoryTranslations);
                DB::commit();
                flash(__('Category has been inserted successfully'))->success();
                return redirect()->route('categories.index');
            } else {
                flash(__('Something went wrong'))->error();
                return back();
            }
        } catch(Exception $e) {
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
        $category = $this->categoryService->get(decrypt($id));
        $reviewCategories = \App\Models\ReviewCategory::where('enabled', true)->orderBy('title')->get();
        return view('categories.edit', compact('category', 'reviewCategories'));
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
        try{
            $category = $this->categoryService->get($id);
            if(!$category) return abort(404);

            $inputs = $this->categoryService->formatDataInputs($request, $category);
            $result = $this->categoryService->update($id, $inputs);
            if($result){
                $inputCategoryTranslations  = $this->categoryTranslationService->formatDataInputs($request, $category->id);
                $this->categoryTranslationService->updateOrCreateMultiple($inputCategoryTranslations);

                if($request->has('review_categories')){
                    $category->reviewCategories()->sync($request->review_categories);
                } else {
                    $category->reviewCategories()->detach();
                }

                DB::commit();
                flash(__('Category has been updated successfully'))->success();
                return redirect()->route('categories.index');
            }else{
                flash(__('Something went wrong'))->error();
                return back();
            }
        }catch(Exception $e){
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
        try{
            DB::beginTransaction();
            $category       = $this->categoryService->get($id);
            $products       = $category->products;

            if(isset($products) && count($products) > 0){
                flash(__('Category contains products, cannot be deleted'))->error();
                return back();
            }

            if($this->categoryService->destroy($id)){
                DB::commit();
                flash(__('Category has been deleted successfully'))->success();
                return redirect()->route('categories.index');
            }
        }catch(Exception $e){
            DB::rollBack();
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function updateFeatured(Request $request)
    {
        $status = (boolean)$this->categoryService->updateFeatured($request->id);
        if ($status) {
            $response = [
                'status'  => 1,
                'message' => 'Featured categories updated successfully!'
            ];
        } else {
            $response = [
                'status'  => 0,
                'message' => 'Something went wrong!'
            ];
        }
        return json_encode($response);
    }

    public function updateStatus(Request $request)
    {
        $status = (boolean)$this->categoryService->updateStatus($request->id);
        if ($status) {
            $response = [
                'status'  => 1,
                'message' => 'Published categories updated successfully!'
            ];
        } else {
            $response = [
                'status'  => 0,
                'message' => 'Something went wrong!'
            ];
        }
        return json_encode($response);
    }

    public function exportAll()
    {
        return Excel::download(new CategoryExport, 'categories_export.xlsx');
    }
}

