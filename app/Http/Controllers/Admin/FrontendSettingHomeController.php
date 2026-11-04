<?php
namespace App\Http\Controllers\Admin;

use App\Models\CustomPage\Page;
use App\Repositories\PageRepository;
use App\Services\BannerService;
use App\Services\CustomPage\BlockService;
use App\Services\SliderService;
use Illuminate\Http\Request;

class FrontendSettingHomeController extends Controller
{
    protected $sliderService;
    protected $homeBannerService;
    protected $blockService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SliderService $sliderService, BannerService $homeBannerService, BlockService $blockService)
    {
        $this->sliderService = $sliderService;
        $this->homeBannerService = $homeBannerService;
        $this->blockService = $blockService;
    }

    public function index()
    {
        $page = Page::where('type', '=', Page::TYPE_HOMEPAGE)->first();

        $sliderList = $this->sliderService->getList([
            'perPage' => 0,
        ]);

        $homeBanner1List = $this->homeBannerService->getList([
            'position' => 1,
        ]);

        $homeBanner2List = $this->homeBannerService->getList([
            'position' => 2
        ]);

        return view('frontend_settings.home.index', [
            'sliderList' => $sliderList,
            'homeBanner1List' => $homeBanner1List,
            'homeBanner2List' => $homeBanner2List,
            'page' => $page,
            'blocks' => $page->allBlocks,
            'blockTypes' => $this->blockService->getListType(),
            'allBlocks' => $this->blockService->getList(['keyword' => '']),
        ]);
    }
}

