<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\CustomPage\PageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PageService as PageTableService;
use App\Services\PageTranslationService;

class PageController extends Controller
{
    protected $pageService;
    protected $pageTableService;
    protected $pageTranslationService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PageService $pageService,
        PageTableService $pageTableService,
        PageTranslationService $pageTranslationService
    ) {
        //$this->middleware('auth');
        $this->pageService              = $pageService;
        $this->pageTableService         = $pageTableService;
        $this->pageTranslationService   = $pageTranslationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Page::all();
        return view('pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.form');
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

            $checkIssetPage = $this->pageTableService->getAllWithConditions(['slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug))]);
            if(isset($checkIssetPage) && count($checkIssetPage) > 0){
                flash(__('Slug has been used already'))->warning();
                return back();
            }

            $inputs     = $this->pageTableService->formatDataInputs($request);
            $page       = $this->pageTableService->create($inputs);

            if ($page) {
                $inputPageTranslations  = $this->pageTranslationService->formatDataInputs($request, $page->id);
                $this->pageTranslationService->insert($inputPageTranslations);
                DB::commit();
                flash(__('New page has been created successfully'))->success();
                return redirect()->route('pages.index');
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
        $page = Page::where('slug', $id)->first();
        if ($page != null) {
            return view('pages.form', compact('page'));
        }
        abort(404);
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
            $page = $this->pageTableService->get($id);
            if(!$page) return abort(404);

            $checkIssetPage = Page::where(['slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug))])->where('id','<>',$page->id)->get();
            if(isset($checkIssetPage) && count($checkIssetPage) > 0){
                flash(__('Slug has been used already'))->warning();
                return back();
            }

            $inputs = $this->pageTableService->formatDataInputs($request, $page);
            $result = $this->pageTableService->update($id, $inputs);

            if($result){
                $inputPageTranslations  = $this->pageTranslationService->formatDataInputs($request, $page->id);
                $this->pageTranslationService->updateOrCreateMultiple($inputPageTranslations);

                DB::commit();
                flash(__('New page has been updated successfully'))->success();
                return redirect()->route('pages.index');
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
        if (Page::destroy($id)) {
            flash(__('Page has been deleted successfully'))->success();
            return redirect()->back();
        }
        return back();
    }

    public function show_custom_page($slug, Request $request)
    {
        $page = $this->pageService->getBySlug($slug);
        if ($page != null) {
            return $this->showYsgPage($page, $request);
        }

        $page = Page::where('slug', $slug)->first();
        if ($page != null) {
            return view('frontend.custom_page', compact('page'));
        }
        abort(404);
    }

    private function showYsgPage($page, $request)
    {
        $validate = $this->pageService->checkAccessPermission($page, $request);
        if ($validate['valid'] == false) {
            flash($validate['message'], 'warning');
            session(['previous_url' => route('custom-pages.show_custom_page', $page->slug)]);
            return redirect($validate['redirect_url']);
        }

        if (session('previous_url') != null) {
            session()->forget('previous_url');
        }

        $blocks = $page->blocks;
        $template = 'frontend.custom_page.' . $page->type . '.index';

        return view($template, compact('page', 'blocks'));
    }
}
