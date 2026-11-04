<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Services\SliderService;
use App\Services\CategoryService;
use App\Services\BrandService;
use App\Services\SellerService;
use App\Services\HomeCategoryService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Mobile\NewsletterConfigResource;
use App\Http\Resources\Mobile\TopCategoryResource;
use App\Http\Resources\Mobile\TopBrandResource;
use App\Http\Resources\Mobile\SellerResource;
use App\Http\Resources\Mobile\ProductResource;
use App\Http\Resources\Mobile\SliderResource;
use App\Http\Resources\Mobile\BlockResource;
use App\Http\Resources\Mobile\HomeCategoryResource;
use App\Services\ProductService;
use App\Models\CustomPage\Page;
use App\Models\CustomPage\Block;
use App\Models\HomeCategory;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Homepage",
 *     description="Mobile API endpoints for homepage"
 * )
 * 
 */
class HomeController extends Controller
{
    protected $categoryService;
    protected $brandService;
    protected $sellerService;
    protected $productService;
    protected $homeCategoryService;

    public function __construct(CategoryService $categoryService, BrandService $brandService, SellerService $sellerService, ProductService $productService, HomeCategoryService $homeCategoryService)
    {
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;
        $this->sellerService = $sellerService;
        $this->productService = $productService;
        $this->homeCategoryService = $homeCategoryService;
    }
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/home/slider-block",
     *     operationId="mobileSliderBlock",
     *     tags={"Homepage"},
     *     summary="Get slider block for homepage",
     *     description="Returns list of published sliders for homepage banner display",
     *     @OA\Response(
     *         response=200,
     *         description="List of published sliders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Slider block"),
     *             @OA\Property(
     *                 property="data", 
     *                 type="array", 
     *                 @OA\Items(ref="#/components/schemas/Slider"),
     *                 description="Array of slider objects"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function slider_block(Request $request)
    {
        $sliderService = app()->make(SliderService::class);
        $sliders = $sliderService->getPublishedList()->filter(function ($slider) {
            return !$slider->isExpired();
        });

        return $this->successResponse('Slider block', SliderResource::collection($sliders));
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/home/popup-image",
     *     operationId="mobilePopupImage",
     *     tags={"Homepage"},
     *     summary="Get popup image configuration",
     *     description="Returns newsletter configuration for popup image display",
     *     @OA\Response(
     *         response=200,
     *         description="Newsletter configuration retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Newsletter configuration retrieved"),
     *             @OA\Property(property="data", ref="#/components/schemas/NewsletterConfig")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Newsletter configuration not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Newsletter configuration not found")
     *         )
     *     )
     * )
     */
    public function popupImage(Request $request)
    {
        $newsletter = DB::table('newsletter_config')->where('id', 1)->first();

        if (!$newsletter) {
            return $this->errorResponse('Newsletter configuration not found', [], 404);
        }

        return $this->successResponse('Newsletter configuration retrieved', new NewsletterConfigResource($newsletter));
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/categories/top",
     *     operationId="getMobileTopCategories",
     *     tags={"Homepage"},
     *     summary="Get top categories",
     *     description="Returns a list of top categories for mobile application",
     *     @OA\Response(
     *         response=200,
     *         description="Top categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Top categories retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TopCategory")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve top categories")
     *         )
     *     )
     * )
     */
    public function getTopCategories(Request $request)
    {
        try {
            $topCategories = $this->categoryService->getTopCategories();
            
            return $this->successResponse(
                'Top categories retrieved successfully',
                TopCategoryResource::collection($topCategories)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve top categories', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/brands/top",
     *     operationId="getMobileTopBrands",
     *     tags={"Homepage"},
     *     summary="Get top brands",
     *     description="Returns a list of top brands for mobile application",
     *     @OA\Response(
     *         response=200,
     *         description="Top brands retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Top brands retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TopBrand")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve top brands")
     *         )
     *     )
     * )
     */
    public function getTopBrands(Request $request)
    {
        try {
            $topBrands = $this->brandService->getTopBrands();
            
            return $this->successResponse(
                'Top brands retrieved successfully',
                TopBrandResource::collection($topBrands)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve top brands', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/home/shop-by-stores",
     *     operationId="getMobileShopByStores",
     *     tags={"Homepage"},
     *     summary="Get shop by stores",
     *     description="Returns a list of shops/stores for mobile application",
     *     @OA\Response(
     *         response=200,
     *         description="Shop by stores retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shop by stores retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Seller")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve shop by stores")
     *         )
     *     )
     * )
     */
    public function shopByStores(Request $request)
    {
        try {
            $bestSellers = $this->sellerService->getBestSellers();
            
            return $this->successResponse(
                'Shop by stores retrieved successfully',
                SellerResource::collection($bestSellers)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve shop by stores', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/home/best-selling",
     *     operationId="mobileBestSellingProducts",
     *     tags={"Homepage"},
     *     summary="Get best selling products",
     *     description="Retrieve the best selling products for mobile application",
     *     @OA\Response(
     *         response=200,
     *         description="Best selling products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Best selling products retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve best selling products")
     *         )
     *     )
     * )
     */
    public function bestSellingProducts(Request $request)
    {
        try {
            $products = $this->productService->getHomeBestSellingProducts();
            
            return $this->successResponse(
                'Best selling products retrieved successfully',
                ProductResource::collection($products)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve best selling products', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/home/featured",
     *     operationId="getMobileFeaturedProducts",
     *     tags={"Homepage"},
     *     summary="Get featured products",
     *     description="Returns a list of featured products for the mobile homepage",
     *     @OA\Response(
     *         response=200,
     *         description="Featured products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Feature products retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve feature products")
     *         )
     *     )
     * )
     */
    public function getFeaturedProducts(Request $request)
    {
        try {
            $list = $this->productService->getHomeFeaturedProducts($request);

            return $this->successResponse(
                'Feature products retrieved successfully',
                ProductResource::collection($list)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve feature products', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/home/blocks",
     *     operationId="mobileHomepageBlocks",
     *     tags={"Homepage"},
     *     summary="Get homepage blocks",
     *     description="Returns list of homepage blocks with their content",
     *     @OA\Response(
     *         response=200,
     *         description="Homepage blocks retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Homepage blocks retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Block")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve homepage blocks")
     *         )
     *     )
     * )
     */
    public function getHomepageBlocks(Request $request)
    {
        $page = Page::where('type', '=', Page::TYPE_HOMEPAGE)->first();
        try {
            $blocks = $page->blocks->filter(function ($block) {
                $blockContents = $block->blockContents->filter(function ($blockContent) {
                    return $blockContent->is_enabled && !$blockContent->isExpired();
                });
                return !$block->isExpired() && (!$block->needsContent() || $blockContents->isNotEmpty());
            });

            return $this->successResponse(
                'Homepage blocks retrieved successfully',
                BlockResource::collection($blocks)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve homepage blocks', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/home/categories",
     *     operationId="mobileHomeCategories",
     *     tags={"Homepage"},
     *     summary="Get all home categories with products",
     *     description="Returns list of all active home categories with status = 1, including their products (cached for 2 hours)",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Home categories retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/HomeCategoryResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to retrieve home categories"
     *             )
     *         )
     *     )
     * )
     */
    public function getHomeCategories()
    {
        try {
            $homeCategories = $this->homeCategoryService->getList();
            $products = [];
            $cacheKey = 'mobile_home_categories_section';
            
            if (Cache::has($cacheKey)) {
                $products = Cache::get($cacheKey);
            } else {
                foreach ($homeCategories as $key => $homeCategory) {
                    $products[$homeCategory->category->id] = $this->productService->getHomeCategoryProducts($homeCategory->category->id);
                }
                Cache::put($cacheKey, $products, 7200);
            }

            $homeCategoriesWithProducts = $homeCategories->map(function ($homeCategory) use ($products) {
                $homeCategory->products = $products[$homeCategory->category->id] ?? collect();
                return $homeCategory;
            });

            return $this->successResponse(
                'Home categories retrieved successfully',
                HomeCategoryResource::collection($homeCategoriesWithProducts)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve home categories', 500);
        }
    }

    
}