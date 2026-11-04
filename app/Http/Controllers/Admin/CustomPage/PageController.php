<?php
namespace App\Http\Controllers\Admin\CustomPage;

use App\Http\Controllers\Admin\Controller;
use App\Repositories\CustomPage\SellerCorporateDomainRepository;
use App\Services\CustomPage\BlockService;
use App\Services\CustomPage\PageService;
use App\Services\CustomPage\YsgPageTranslationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    protected $service;
    protected $blockService;
    protected $ysgPageTranslationService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PageService $service, BlockService $blockService, YsgPageTranslationService $ysgPageTranslationService)
    {
        $this->service                      = $service;
        $this->blockService                 = $blockService;
        $this->ysgPageTranslationService    = $ysgPageTranslationService;
    }

    public function index()
    {
        $pages = $this->service->getList();
        return view('admin.custom_page.page.index', [
            'pageList' => $pages,
            'service' => $this->service
        ]);
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->all();
            $model = $this->service->create($data);
            if ($model) {
                $inputYsgPageTranslations  = $this->ysgPageTranslationService->formatDataInputs($request, $model->id);
                $this->ysgPageTranslationService->insert($inputYsgPageTranslations);

                DB::commit();
                flash(__('Block has been inserted successfully'))->success();
                return redirect()->route('admin.custom_page.page.edit', $model->id);
            }else{
                DB::rollBack();
                flash(__('Something went wrong'))->error();
                return back();
            }
            return redirect()->route('admin.custom_page.page.index');

        }catch(Exception $e){
            DB::rollBack();
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function edit($id)
    {
        $model = $this->service->get($id);
        if (is_null($model)) {
            return redirect()->route('admin.custom_page.page.index');
        }

        $template = 'admin.custom_page.page.edit';

        $domainRepo = new SellerCorporateDomainRepository();
        $corporateDomains = $domainRepo->getList();
        $blockTypes = $this->blockService->getListType();

        return view($template, [
            'page' => $model,
            'service' => $this->service,
            'blockTypes' => $blockTypes,
            'blocks' => $model->allBlocks??[],
            'allBlocks' => $this->blockService->getBlockListForPage(),
            'corporateDomains' => $corporateDomains,
        ]);
    }

    public function update($id, Request $request)
    {
        try{
            DB::beginTransaction();
            $page = $this->service->get($id);
            if (is_null($page)) {
                return redirect()->route('admin.custom_page.page.index');
            }
            $data = $request->all();
            $inputs = $this->service->formatDataInputs((object)$data);

            $result = $this->service->update($id, $inputs);
            if($result){
                $inputYsgPageTranslations  = $this->ysgPageTranslationService->formatDataInputs($request, $page->id);
                $this->ysgPageTranslationService->updateOrCreateMultiple($inputYsgPageTranslations);

                DB::commit();
                flash(__('Page has updated successfully'))->success();
                return redirect()->route('admin.custom_page.page.index');
            }else{
                DB::rollBack();
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
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->service->destroy($id);
        return redirect()->route('admin.custom_page.page.index');
    }
}

