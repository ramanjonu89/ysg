<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Services\ShopService;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class MicrositeController extends Controller
{
    protected $shopService;

    /**
     *
     * @return void
     */
    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function index(Request $request, $slug)
    {
        $shop = Shop::where('slug', $slug)->first();
        // $shop = $this->shopService->getBySlug($slug);
        if (!$shop) {
            return redirect()->route('home');
        }
        if (!($microsite = $shop->user->seller->microsite)) {
            return redirect()->route('home');
        }

        if (!$microsite->active) {
            return redirect()->route('home');
        }

        session(['isMicrosite' => true]);
        session(['shop' => $shop]);
        session(['microsite' => $microsite]);

        $blocks = $microsite->blocks()->with('blockContents')->get();

        $products = $this->shopService->getProducts($shop);

        $allBrandsAndCategories = DB::table('view_product_full')
            ->where('published', 1)
            ->where('user_id', $shop->user->id)
            ->select(['category_id', 'brand_id'])
            ->distinct()
            ->get()
            ->toArray();
        $categories = Category::where('status', '=', 1)
            ->whereIn('id', array_column($allBrandsAndCategories, 'category_id'))
            ->get();
        $brands = DB::table('brands')
            ->whereIn('id', array_column($allBrandsAndCategories, 'brand_id'))
            ->select(['brands.id', 'brands.name'])
            ->get()
            ->toArray();

        return view('frontend.microsite.index', compact([
            'shop',
            'microsite',
            'blocks',
            'products',
            'categories',
            'brands',
        ]));
    }

    public function page($slug, $page)
    {
        switch ($page) {
            case 'terms-and-conditions':
                $content = getMicrosite()->terms_and_conditions;
                break;

            case 'privacy-policy':
                $content = getMicrosite()->policy;
                break;

            case 'return-policy':
                $content = getMicrosite()->return_policy;
                break;

            default:
                $content = getMicrosite()->terms_and_conditions;
        }

        return view('frontend.microsite.page', compact('content'));
    }
}

