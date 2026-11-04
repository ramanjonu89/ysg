<?php
namespace App\Http\Controllers\Admin\CustomPage;

use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\BlockContentRequest;
use App\Models\CustomPage\Block;
use App\Models\CustomPage\BlockContent;
use App\Services\CustomPage\BlockContentService;
use App\Services\CustomPage\BlockService;
use App\Services\CustomPage\YsgBlockContentTranslationService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

use function Symfony\Component\String\b;

class BlockContentController extends Controller
{
    protected $service;
    protected $blockService;
    protected $ysgBlockContentTranslationService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BlockContentService $service, BlockService $blockService, YsgBlockContentTranslationService $ysgBlockContentTranslationService)
    {
        $this->service                              = $service;
        $this->blockService                         = $blockService;
        $this->ysgBlockContentTranslationService    = $ysgBlockContentTranslationService;
    }

    /**
     * @param  Block  $block
     * @return Application|Factory|View
     */
    public function create(Block $block)
    {
        $daterange = '';
        $template = 'admin.custom_page.block.item';
        if (in_array($block->type, [Block::TYPE_BANNER, Block::TYPE_IMAGE_LINK, Block::TYPE_SHOP_LIST, Block::TYPE_CA_SHOP_LIST, Block::TYPE_MICROSITE_SLIDER])) {
            $template = 'admin.custom_page.block.'.$block->type.'.item';
        }
        $formAction = route('admin.custom_page.block.item.store', $block->id);
        return view($template, [
            'formAction'    => $formAction,
            'block'         => $block,
            'service'       => $this->service,
            'blockContent'  => new BlockContent(),
            'daterange'     => $daterange
        ]);
    }

    /**
     * @param $image
     * @return string
     */
    private function storeImage($image): string
    {
        $image_file_name = time().rand(11111,99999).$image->getClientOriginalName();
        $block_content_image_path = config('constant.block_content_image_path');
        if (!File::isDirectory($block_content_image_path)) {
            File::makeDirectory($block_content_image_path, 0775, true);
        }
        $image->move($block_content_image_path, $image_file_name);
        return '/' . $block_content_image_path . '/' . $image_file_name;
    }

    /**
     * @param  Block  $block
     * @param  BlockContentRequest  $request
     * @return RedirectResponse
     */
    public function store(Block $block, BlockContentRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->all();
            $inputs         = $this->service->formatDataInputs((object) $request->all());
            $inputs['block_id'] = $block->id;

            if (isset($data['image'])) {
                $image = $data['image'];
                unset($data['image']);
            }
            $blockContent   = $this->service->create($inputs);

            if ($blockContent) {
                if (isset($image)) {
                    $imagePath = $this->storeImage($image);
                    $this->service->update($blockContent->id, [
                        'image' => $imagePath,
                    ]);
                }
                $inputYsgBlockContentTranslations         = $this->ysgBlockContentTranslationService->formatDataInputs($request,$blockContent->id);
                $this->ysgBlockContentTranslationService->insert($inputYsgBlockContentTranslations);

                DB::commit();
                flash(__('Item Block has been inserted successfully'))->success();
                return redirect()->route('admin.custom_page.block.edit', $block->id);
            } else {
                DB::rollBack();
                flash(__('Something went wrong'))->error();
                return back();
            }
        } catch(Exception $e) {
            DB::rollBack();
            flash(__('Something went wrong' . $e->getMessage()))->error();
            return back();
        }

    }

    /**
     * @param  Block  $block
     * @param  BlockContent  $content
     * @return Application|Factory|RedirectResponse|View
     */
    public function edit(Block $block, BlockContent $content)
    {
        $daterange = '';
        $template = 'admin.custom_page.block.item';
        if (in_array($block->type, [Block::TYPE_BANNER, Block::TYPE_IMAGE_LINK, Block::TYPE_SHOP_LIST, Block::TYPE_CA_SHOP_LIST, Block::TYPE_MICROSITE_SLIDER])) {
            $template = 'admin.custom_page.block.'.$block->type.'.item';
        }
        $formAction = route('admin.custom_page.block.item.update', ['block' => $block, 'content' => $content]);

        if (!empty($content->start_date) && !empty($content->end_date)) {
            $daterange = date('d/m/Y H:i', $content->start_date).' - '.date('d/m/Y H:i', $content->end_date);
        }

        return view($template, [
            'formAction' => $formAction,
            'block'      => $block,
            'service'    => $this->service,
            'blockContent' => $content,
            'daterange'    => $daterange,
        ]);
    }

    /**
     * @param  Block  $block
     * @param  BlockContent  $content
     * @param  BlockContentRequest  $request
     * @return RedirectResponse
     */
    public function update(Block $block, BlockContent $content, BlockContentRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {

            $inputs = $this->service->formatDataInputs($request);
            $data = $request->all();
            if (isset($data['image'])) {
                $inputs['image'] = $this->storeImage($data['image']);
            }

            $result = $this->service->update($content->id, $inputs);
            if($result){
                $inputYsgBlockContentTranslations  = $this->ysgBlockContentTranslationService->formatDataInputs($request, $content->id);
                $this->ysgBlockContentTranslationService->updateOrCreateMultiple($inputYsgBlockContentTranslations);
                DB::commit();
                flash(__('Item Block has updated successfully'))->success();
                return redirect()->route('admin.custom_page.block.edit', $block->id);
            }else{
                DB::rollBack();
                flash(__('Something went wrong'))->error();
                return back();
            }
        }catch(Exception $e){
            DB::rollBack();
            flash(__('Something went wrong' . $e->getMessage()))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $block
     * @param  BlockContent  $content
     * @return RedirectResponse
     */
    public function destroy(Block $block, BlockContent $content): RedirectResponse
    {
        $this->service->destroy($content->id);
        return redirect()->route('admin.custom_page.block.edit', $block->id);
    }
}

