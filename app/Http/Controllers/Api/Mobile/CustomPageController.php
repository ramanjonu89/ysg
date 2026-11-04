<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Models\CustomPage\Page;
use App\Models\CustomPage\Block;
use App\Http\Resources\Mobile\BlockResource;
use App\Services\CustomPage\PageService;

/**
 * @OA\Tag(
 *     name="Custom Page",
 *     description="Mobile API endpoints for custom pages"
 * )
 */
class CustomPageController extends Controller
{
    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/custom-page",
     *     operationId="getCustomPageBySlug",
     *     tags={"Custom Page"},
     *     summary="Get custom page by slug",
     *     description="Returns custom page information with its blocks based on the provided slug parameter",
     *     @OA\Parameter(
     *         name="slug",
     *         in="query",
     *         description="Slug of the custom page to retrieve",
     *         required=true,
     *         @OA\Schema(type="string", example="about-us")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Custom page retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Custom page retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="About Us"),
     *                 @OA\Property(property="slug", type="string", example="about-us"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="pages/about-us.jpg"),
     *                 @OA\Property(property="description", type="string", example="About our company"),
     *                 @OA\Property(property="content", type="string", example="Full page content"),
     *                 @OA\Property(property="type", type="string", example="page"),
     *                 @OA\Property(property="is_enabled", type="boolean", example=true),
     *                 @OA\Property(property="need_login", type="boolean", example=false),
     *                 @OA\Property(property="meta_title", type="string", example="About Us - YSG"),
     *                 @OA\Property(property="meta_description", type="string", example="Learn more about our company"),
     *                 @OA\Property(property="meta_keywords", type="string", example="about, company, ysg"),
     *                 @OA\Property(
     *                     property="blocks",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Block")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Missing slug parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Slug parameter is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Page not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Page not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve custom page")
     *         )
     *     )
     * )
     */
    public function getCustomPageBySlug(Request $request)
    {
        $slug = $request->query('slug');

        if (!$slug) {
            return $this->errorResponse('Slug parameter is required', [], 400);
        }

        try {
            $page = $this->pageService->getBySlug($slug);

            if (!$page) {
                return $this->errorResponse('Page not found', [], 404);
            }

            $blocks = $page->blocks->filter(function ($block) {
                if ($block->is_enabled && $block->type == Block::TYPE_SHOP_LIST) {
                    return true;
                }
                $blockContents = $block->blockContents->filter(function ($blockContent) {
                    return $blockContent->is_enabled && !$blockContent->isExpired();
                });
                return !$block->isExpired() && (!$block->needsContent() || $blockContents->isNotEmpty());
            });

            $response = [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'image' => $page->image ? url($page->image) : null,
                'description' => $page->description,
                'content' => $page->content,
                'type' => $page->type,
                'is_enabled' => $page->is_enabled,
                'need_login' => $page->need_login,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'meta_keywords' => $page->meta_keywords,
                'blocks' => BlockResource::collection($blocks)
            ];

            return $this->successResponse(
                'Custom page retrieved successfully',
                $response
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve custom page', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/static-page/faqs",
     *     operationId="getFaqsPage",
     *     tags={"Custom Page"},
     *     summary="Get FAQs static page",
     *     description="Returns FAQs page information with its blocks. This endpoint specifically retrieves the page with slug 'faqs' from ysg_pages table.",
     *     @OA\Response(
     *         response=200,
     *         description="FAQs page retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="FAQs page retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Frequently Asked Questions"),
     *                 @OA\Property(property="slug", type="string", example="faqs"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="pages/faqs.jpg"),
     *                 @OA\Property(property="description", type="string", example="Common questions and answers"),
     *                 @OA\Property(property="content", type="string", example="Full FAQs content"),
     *                 @OA\Property(property="type", type="string", example="page"),
     *                 @OA\Property(property="is_enabled", type="boolean", example=true),
     *                 @OA\Property(property="need_login", type="boolean", example=false),
     *                 @OA\Property(property="meta_title", type="string", example="FAQs - YSG"),
     *                 @OA\Property(property="meta_description", type="string", example="Frequently asked questions"),
     *                 @OA\Property(property="meta_keywords", type="string", example="faq, questions, help"),
     *                 @OA\Property(
     *                     property="blocks",
     *                     type="object",
     *                     description="FAQs blocks organized by category",
     *                     @OA\Property(
     *                         property="general-enquiries",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="General Enquiries"),
     *                         @OA\Property(property="content", type="string", example="FAQ content for general enquiries")
     *                     ),
     *                     @OA\Property(
     *                         property="orders",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Orders"),
     *                         @OA\Property(property="content", type="string", example="FAQ content for orders")
     *                     ),
     *                     @OA\Property(
     *                         property="payment",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Payment"),
     *                         @OA\Property(property="content", type="string", example="FAQ content for payment")
     *                     ),
     *                     @OA\Property(
     *                         property="shipping-and-delivery",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Shipping & Delivery"),
     *                         @OA\Property(property="content", type="string", example="FAQ content for shipping and delivery")
     *                     ),
     *                     @OA\Property(
     *                         property="returns-refunds",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Returns & Refunds"),
     *                         @OA\Property(property="content", type="string", example="FAQ content for returns and refunds")
     *                     ),
     *                     @OA\Property(
     *                         property="data-privacy",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Data Privacy"),
     *                         @OA\Property(property="content", type="string", example="FAQ content for data privacy")
     *                     ),
     *                     @OA\Property(
     *                         property="corporate-store",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Corporate Store"),
     *                         @OA\Property(property="content", type="string", example="FAQ content for corporate store")
     *                     ),
     *                     @OA\Property(
     *                         property="private-sales",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Private Sales"),
     *                         @OA\Property(property="content", type="string", example="FAQ content for private sales")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - FAQs page not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="FAQs page not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve FAQs page")
     *         )
     *     )
     * )
     */
    public function getFaqsPage()
    {
        try {
            $page = $this->pageService->getBySlug('faqs');

            if (!$page) {
                return $this->errorResponse('FAQs page not found', [], 404);
            }

            $blocks = $page->blocks->filter(function ($block) {
                $blockContents = $block->blockContents->filter(function ($blockContent) {
                    return $blockContent->is_enabled && !$blockContent->isExpired();
                });
                return !$block->isExpired() && (!$block->needsContent() || $blockContents->isNotEmpty());
            });

            $contents = [];

            foreach ($blocks as $block) {
                $contents[$block->title] = [
                    'title' => $this->_getFaqsTitle($block->title),
                    'content' => $block->content
                ];
            }

            $response = [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'image' => $page->image ? url($page->image) : null,
                'description' => $page->description,
                'content' => $page->content,
                'type' => $page->type,
                'is_enabled' => $page->is_enabled,
                'need_login' => $page->need_login,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'meta_keywords' => $page->meta_keywords,
                'blocks' => $contents
            ];

            return $this->successResponse(
                'FAQs page retrieved successfully',
                $response
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve FAQs page', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/static-page/term-conditions",
     *     operationId="getTermConditionsPage",
     *     tags={"Custom Page"},
     *     summary="Get Terms & Conditions static page",
     *     description="Returns Terms & Conditions page information with its blocks. This endpoint specifically retrieves the page with slug 'term-conditions' from ysg_pages table.",
     *     @OA\Response(
     *         response=200,
     *         description="Terms & Conditions page retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Terms & Conditions page retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Terms & Conditions"),
     *                 @OA\Property(property="slug", type="string", example="term-conditions"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="pages/term-conditions.jpg"),
     *                 @OA\Property(property="description", type="string", example="Terms and conditions of service"),
     *                 @OA\Property(property="content", type="string", example="Full terms and conditions content"),
     *                 @OA\Property(property="type", type="string", example="page"),
     *                 @OA\Property(property="is_enabled", type="boolean", example=true),
     *                 @OA\Property(property="need_login", type="boolean", example=false),
     *                 @OA\Property(property="meta_title", type="string", example="Terms & Conditions - YSG"),
     *                 @OA\Property(property="meta_description", type="string", example="Terms and conditions of service"),
     *                 @OA\Property(property="meta_keywords", type="string", example="terms, conditions, legal"),
     *                 @OA\Property(
     *                     property="blocks",
     *                     type="object",
     *                     description="Terms & Conditions blocks organized by category",
     *                     @OA\Property(
     *                         property="term-of-use",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Term of Use"),
     *                         @OA\Property(property="content", type="string", example="Term of use content")
     *                     ),
     *                     @OA\Property(
     *                         property="terms-and-conditions",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Terms & Conditions"),
     *                         @OA\Property(property="content", type="string", example="Terms and conditions content")
     *                     ),
     *                     @OA\Property(
     *                         property="privacy-policy",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Privacy Policy"),
     *                         @OA\Property(property="content", type="string", example="Privacy policy content")
     *                     ),
     *                     @OA\Property(
     *                         property="adult-products",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Adult Products"),
     *                         @OA\Property(property="content", type="string", example="Adult products content")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Terms & Conditions page not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Terms & Conditions page not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve Terms & Conditions page")
     *         )
     *     )
     * )
     */
    public function getTermConditionsPage()
    {
        try {
            $page = $this->pageService->getBySlug('term-conditions');

            if (!$page) {
                return $this->errorResponse('Terms & Conditions page not found', [], 404);
            }

            $blocks = $page->blocks->filter(function ($block) {
                $blockContents = $block->blockContents->filter(function ($blockContent) {
                    return $blockContent->is_enabled && !$blockContent->isExpired();
                });
                return !$block->isExpired() && (!$block->needsContent() || $blockContents->isNotEmpty());
            });

            $contents = [];

            foreach ($blocks as $block) {
                $contents[$block->title] = [
                    'title' => $this->_getTermConditionsTitle($block->title),
                    'content' => $block->content
                ];
            }

            $response = [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'image' => $page->image ? url($page->image) : null,
                'description' => $page->description,
                'content' => $page->content,
                'type' => $page->type,
                'is_enabled' => $page->is_enabled,
                'need_login' => $page->need_login,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'meta_keywords' => $page->meta_keywords,
                'blocks' => $contents
            ];

            return $this->successResponse(
                'Terms & Conditions page retrieved successfully',
                $response
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve Terms & Conditions page', [], 500);
        }
    }

    private function _getFaqsTitle($title) {
        switch ($title) {
            case 'general-enquiries':
                return 'General Enquiries';
            case 'orders':
                return 'Orders';
            case 'payment':
                return 'Payment';
            case 'shipping-and-delivery':
                return 'Shipping & Delivery';
            case 'returns-refunds':
                return 'Returns & Refunds';
            case 'data-privacy':
                return 'Data Privacy';
            case 'corporate-store':
                return 'Corporate Store';
            case 'private-sales':
                return 'Private Sales';
            default:
                return $title;
        }
    }

    private function _getTermConditionsTitle($title) {
        switch ($title) {
            case 'term-of-use':
                return 'Term of Use';
            case 'terms-and-conditions':
                return 'Terms & Conditions';
            case 'privacy-policy':
                return 'Privacy Policy';
            case 'adult-products':
                return 'Adult Products';
            case 'return-policy':
                return 'Return Policy';
            default:
                return $title;
        }
    }
}

