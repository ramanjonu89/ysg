<?php
namespace App\Http\Controllers\Admin\CustomPage;

use App\Http\Controllers\Admin\Controller;
use App\Models\CustomPage\Block;
use App\Services\BrandService;
use App\Services\CustomPage\BlockService;
use App\Services\CustomPage\YsgBlockTranslationService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Image;

class BlockController extends Controller
{
    protected $brandService;
    protected $service;
    protected $ysgBlockTranslationService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BrandService $brandService,
        BlockService $service, 
        YsgBlockTranslationService $ysgBlockTranslationService
    ) {
        $this->brandService = $brandService;
        $this->service = $service;
        $this->ysgBlockTranslationService = $ysgBlockTranslationService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $list = $this->service->getList(['keyword' => $keyword]);
        return view('admin.custom_page.block.index', [
            'blockList'     => $list,
            'blockTypes'    => $this->service->getAllListType(),
            'keyword'       => $keyword,
        ]);
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $model  = $this->service->create((object) $request->all());

            if ($model) {
                $inputYsgBlockTranslations  = $this->ysgBlockTranslationService->formatDataInputs($request, $model->id);
                $this->ysgBlockTranslationService->insert($inputYsgBlockTranslations);

                DB::commit();
                flash(__('Block has been inserted successfully'))->success();
                return redirect()->route('admin.custom_page.block.edit', $model->id);
            }else{
                DB::rollBack();
                flash(__('Something went wrong'))->error();
                return back();
            }

        } catch(Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            flash(__('Something went wrong' . $e->getMessage()))->error();
            return back();
        }
    }

    /**
     * @param  Block  $block
     * @return Application|Factory|View
     */
    public function edit(Block $block)
    {
        switch ($block->type) {
            case Block::TYPE_SHOP_LIST:
                $blockContents = $this->service->getListSeller($block);
                break;
            case Block::TYPE_CA_SHOP_LIST:
                $blockContents = $this->service->getListSeller($block, true);
                break;
            case Block::TYPE_CA_BRAND_LIST:
                $blockContents = $this->brandService->getCAList();
                break;
            default:
                $blockContents = $block->allBlockContents??[];
        }
        $daterange = '';
        // if ($block->type == Block::TYPE_SHOP_LIST) {
        //     $blockContents = $this->service->getListSeller($block);
        // } else if ($block->type == Block::TYPE_CA_SHOP_LIST) {
        //     $blockContents = $this->service->getListSeller($block, true);
        // } else {
        //     $blockContents = $block->allBlockContents??[];
        // }

        $template = 'admin.custom_page.block.edit';
        if (in_array($block->type, [Block::TYPE_BANNER, Block::TYPE_IMAGE_LINK, Block::TYPE_SHOP_LIST,Block::TYPE_TEXT,Block::TYPE_PRODUCT_CAROUSEL,Block::TYPE_VIDEO, Block::TYPE_MICROSITE_SLIDER])) {
            $template = 'admin.custom_page.block.'.$block->type.'.edit';
        }

        if (!empty($block->start_date) && !empty($block->end_date)) {
            $daterange = date('d/m/Y H:i', $block->start_date).' - '.date('d/m/Y H:i', $block->end_date);
        }

        return view($template, [
            'block' => $block,
            'blockTypes' => $this->service->getAllListType(),
            'blockContents' => $blockContents,
            'options' => $block->options,
            'daterange'    => $daterange,
        ]);
    }

    /**
     * @param $image
     * @return string
     */
    private function storeImage($image): string
    {
        $image_file_name = time().rand(11111,99999).$image->getClientOriginalName();
        $block_content_image_path = config('constant.block_image_path');
        if (!File::isDirectory($block_content_image_path)) {
            File::makeDirectory($block_content_image_path, 0775, true);
        }
        $image->move($block_content_image_path, $image_file_name);
        return '/' . $block_content_image_path . '/' . $image_file_name;
    }

    /**
     * @param  Request  $request
     * @param $block
     * @return mixed
     */
    private function saveBackgroundImage(Request $request, $block) {
        $data = $request->all();
        $options = $data['options']??[];

        if (isset($options['mobile_bg'])) {
            $options['mobile_bg'] = $this->storeImage($options['mobile_bg']);
        } else {
            $options['mobile_bg'] = $block->options->mobile_bg;
        }

        if (isset($options['tablet_bg'])) {
            $options['tablet_bg'] = $this->storeImage($options['tablet_bg']);
        } else {
            $options['tablet_bg'] = $block->options->tablet_bg;
        }

        if (isset($options['pc_bg'])) {
            $options['pc_bg'] = $this->storeImage($options['pc_bg']);
        } else {
            $options['pc_bg'] = $block->options->pc_bg;
        }

        return $options;
    }

    private function validateUpdateRequest(Block $block, Request $request): bool
    {
        $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,})([\/\w %&?\[\]=.\'"()-]*)*\/?$/i';

        $rules = [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'options.mobile_bg' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'options.tablet_bg' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'options.pc_bg' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'options.link' => 'nullable|regex:'.$regex,
            'options.bg_padding' => 'regex:/^-?\d+,-?\d+,-?\d+,-?\d+$/i',
        ];

        if ($block->type == Block::TYPE_PRODUCT_CAROUSEL) {
            if (!isset($block->options->mobile_bg)) {
                $rules['options.mobile_bg'] = 'required|'.$rules['options.mobile_bg'];
            }

            if (!isset($block->options->tablet_bg)) {
                $rules['options.tablet_bg'] = 'required|'.$rules['options.tablet_bg'];
            }

            if (!isset($block->options->pc_bg)) {
                $rules['options.pc_bg'] = 'required|'.$rules['options.pc_bg'];
            }
        }

        $messages = [
            'options.mobile_bg.required' => 'Missing Mobile background image',
            'options.tablet_bg.required' => 'Missing Tablet background image',
            'options.pc_bg.required' => 'Missing Tablet background image',
            'options.link.regex' => 'Error Invalid url',
            'options.bg_padding.regex' => 'Background padding format is invalid',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            flash($validator->errors()->first())->error();
            return false;
        }
        return true;
    }

    public function update(Block $block, Request $request)
    {
        if (!$this->validateUpdateRequest($block, $request)) {
            return back()->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->all();

            $inputs = $this->service->formatDataInputs($request, $block);
            if ($block->type == Block::TYPE_PRODUCT_CAROUSEL) {
                $inputs['options'] = $this->saveBackgroundImage($request, $block);
            }

            $result = $this->service->update($block->id, $inputs);

            if ($result) {
                $inputYsgBlockTranslations  = $this->ysgBlockTranslationService->formatDataInputs($request, $block->id);
                $this->ysgBlockTranslationService->updateOrCreateMultiple($inputYsgBlockTranslations);

                DB::commit();
                flash(__('Block has updated successfully'))->success();
                if (!empty($data['redirect'])) {
                    switch ($data['redirect']) {
                        case 'microsite':
                            return redirect()->route('admin.microsites.edit', $block->microsite->id);
                            break;
                        default:
                            return redirect()->route('admin.custom_page.page.edit', $block->page->id);
                    }
                }
                return redirect()->route('admin.custom_page.block.index');
            }
        } catch(Exception $e) {
            DB::rollback();
            flash(__('Something went wrong'))->error();
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $block
     * @return RedirectResponse
     */
    public function destroy(Block $block): RedirectResponse
    {
        $this->service->destroy($block->id);
        return redirect()->route('admin.custom_page.block.index');
    }

    /**
     * change Type
     */
    public function changeType(Request $request): bool
    {
        $block = Block::find($request->block_id);
        if(isset($block)){
            $block->type = $request->type;
            return $block->save();
        }return false;
    }
}

