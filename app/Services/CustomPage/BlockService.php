<?php
namespace App\Services\CustomPage;

use App\Models\CustomPage\Block;
use App\Models\CustomPage\BlockContent;
use App\Repositories\CustomPage\BlockContentRepository;
use App\Repositories\CustomPage\BlockRepository;
use App\Repositories\SellerRepository;
use App\Services\BrandService;
use App\Services\PageServiceFactory;
use App\Models\CustomPage\Page;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class BlockService
{
    protected $repo;
    protected $sellerRepo;
    protected $brandService;

    public function __construct(BlockRepository $repo, SellerRepository $sellerRepo, BrandService $brandService)
    {
        $this->repo = $repo;
        $this->sellerRepo = $sellerRepo;
        $this->brandService = $brandService;
    }

    public function getById($id) {
        return $this->repo->find($id);
    }

    public function getSellerSort($block) {
        if (isset($block->options->itemSort)) {
            $sort = (array)$block->options->itemSort;
            ksort($sort);
        } else $sort = [];
        return $sort;
    }

    public function getListSeller($block, $caList = false) {
        $params = [
            'verification_status' => 1,
            'company_type' => 1,
            'perPage' => 0,
        ];
        if ($caList) {
            $params['company_type'] = 2;
            $params['enable_corporate_domains'] = 1;
        }

        $list = $this->sellerRepo->getList($params)->keyBy('id');

        $sort = $this->getSellerSort($block);

        $result = [];
        foreach ($sort as $id) {
            if(isset($list[$id])){
                $result[] = $list[$id];
                unset($list[$id]);
            }
        }
        foreach ($list as $item) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @return Collection
     */
    public function getCABrandList(): Collection
    {
        $list = $this->brandService->getCAList();

        return $list->mapToGroups(function ($item) {
            $name = $item->name;
            $group = strtoupper($name[0]);
            return [$group => $item];
        });
    }

    public function getAllListType(): array
    {
        return $this->getListType() + $this->getMicrositeListType();
    }

    public function getListType(): array
    {
        return [
            Block::TYPE_IMAGE_LINK      => 'Image with link',
            Block::TYPE_BANNER          => 'Banner',
            Block::TYPE_SHOP_LIST       => 'Flagship Shop list',
            Block::TYPE_CA_SHOP_LIST    => 'CA Shop list',
            Block::TYPE_CA_BRAND_LIST   => 'CA Brand list',
            Block::TYPE_TEXT            => 'Text',
            Block::TYPE_VIDEO           => 'Video',
            Block::TYPE_PRODUCT_CAROUSEL => 'Product Carousel',
        ];
    }

    public function getMicrositeListType(): array
    {
        return [
            Block::TYPE_MICROSITE_SLIDER => 'Slider',
            Block::TYPE_MICROSITE_ONE_IMAGE => 'One image',
            Block::TYPE_MICROSITE_TWO_IMAGE => 'Two images',
            Block::TYPE_MICROSITE_THREE_IMAGE => 'Three images',
            Block::TYPE_MICROSITE_FOUR_IMAGE => 'Four images',
        ];
    }

    public function getShopListType(): array
    {
        return $this->getMicrositeListType();
    }

    public function getList($params) {
        return $this->repo->getList($params);
    }

    public function getBlockListForPage() {
        return $this->repo->getList(['page_type' => 'page']);
    }

    public function getBlockListForMicrosite() {
        return $this->repo->getList(['page_type' => 'microsite']);
    }

    public function getBlockListForShop() {
        return $this->repo->getList(['page_type' => 'shop', 'perPage' => 0]);
    }

    public function getListCorporateSeller($block): array
    {
        return $this->getListSeller($block, true);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create($params) {

        if (is_array($params)) {
            $params = (object) $params;
        }

        $data = [
            'title'         => isset($params->title[0]) ? $params->title[0] : '',
            'type'          => $params->type,
            'page_type'     => isset($params->page_type) ? $params->page_type : Page::TYPE_PAGE,
            'start_date'    => isset($params->start_date) ? $params->start_date : 0,
            'end_date'      => isset($params->end_date) ? $params->end_date : 0
        ];

        $block = $this->repo->create($data);

        if (!empty($params->entity_id)) {
            $factory = new PageServiceFactory();
            $service = $factory->make($params->page_type ?? Page::TYPE_PAGE);
            $entity = $service->get($params->entity_id);
            $numberOfBlocks = $entity->blocks()->count();
            $block->sort = $numberOfBlocks + 1;
            $entity->blocks()->save($block);
            // dd($entity);
            // if ($entity instanceof Page) {
            //     $block->sort = $numberOfBlocks + 1;
            //     $entity->blocks()->save($block);
            // } else {
            //     $newSort = $numberOfBlocks + 1;
            //     $entity->blocks()->attach($block, ['sort', $newSort]);
            // }
            
        }

        return $block;
    }

    public function update($id, $data) {
        return $this->repo->update($id, $data);
    }

    public function destroy($id) {
        return $this->repo->destroy($id);
    }

    public function updateContentIndex($id, $data) {
        $block = $this->getById($id);
        if ($block->type == Block::TYPE_SHOP_LIST || $block->type == Block::TYPE_CA_SHOP_LIST) {
            $options = $block->options;
            $options->itemSort = array_flip($data);
            $this->update($id, [
                'options' => json_encode($options),
            ]);
            return true;
        } else {
            $repo = new BlockContentRepository();
            $list = $repo->getListByBlockId($id);
            if ($list->count()) {
                foreach ($list as $item) {
                    $item->sort = $data[$item->id];
                    $item->save();
                }
                return true;
            }
        }

        return false;
    }

    public function formatDataInputs(object $request, $block) {
        if (isset($request->date_timer_type)) {
            switch ($request->date_timer_type) {
                case Block::START_DATE_ONLY:
                    $start = $request->start_date;
                    break;
                case Block::END_DATE_ONLY:
                    $end = $request->end_date;
                    break;
                default:
                    if (isset($request->daterange)) {
                        list($start, $end) = explode(' - ', $request->daterange);
                    }
                    break;
            }
        }

        $inputs['start_date']       = isset($start) ? Carbon::createFromFormat('d/m/Y H:i:s', $start)->timestamp : 0;
        $inputs['end_date']         = isset($end)   ? Carbon::createFromFormat('d/m/Y H:i:s', $end)->timestamp : 0;

        $inputs['image']        = isset($request->image) ? $this->storeImage($request->image) : $block->image;
        $inputs['is_enabled']   = isset($request->active) && $request->active == 'on' ? true : false;
        $inputs['options']      = isset($request->options) ? json_encode($request->options) : null;

        $inputs['title']            = isset($request->title[0]) ?  $request->title[0] : '';
        $inputs['description']      = isset($request->description[0]) ? $request->description[0] : '';
        $inputs['content']          = isset($request->content[0]) ? $request->content[0] : '';
        if(isset($request->type)){
            $inputs['type']         = $request->type;
        }
        $inputs['page_type']        = isset($request->page_type) ? $request->page_type : '';

        return $inputs;
    }


    private function storeImage($image) {
        $image_file_name = time().rand(11111,99999).$image->getClientOriginalName();
        $block_content_image_path = config('constant.block_image_path');
        if (!File::isDirectory($block_content_image_path)) {
            File::makeDirectory($block_content_image_path, 0775, true);
        }
        $image->move($block_content_image_path, $image_file_name);
        return '/' . $block_content_image_path . '/' . $image_file_name;
    }
}