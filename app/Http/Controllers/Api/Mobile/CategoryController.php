<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Services\Api\Mobile\CategoryService;

/**
 * @OA\Tag(
 *     name="Category",
 *     description="Mobile API endpoints for category management"
 * )
 */
class CategoryController extends Controller
{
    protected $service;
    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/categories",
     *     operationId="mobileCategoryList",
     *     tags={"Category"},
     *     summary="Get category list",
     *     description="Returns a paginated list of categories with nested subcategories",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="show_level",
     *         in="query",
     *         required=false,
     *         description="Levels of subcategories to show (1-3)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Shoes"),
     *                         @OA\Property(property="is_age_restricted", type="boolean", example=false),
     *                         @OA\Property(property="slug", type="string", example="shoes"),
     *                         @OA\Property(property="banner", type="string", example="https://example.com/uploads/categories/banner.png"),
     *                         @OA\Property(property="icon", type="string", example="https://example.com/uploads/categories/icon.png"),
     *                         @OA\Property(
     *                             property="meta_title",
     *                             oneOf={
     *                                 @OA\Schema(type="string", example="Category meta title"),
     *                                 @OA\Schema(type="null", example=null)
     *                             }
     *                         ),
     *                         @OA\Property(
     *                             property="meta_description",
     *                             oneOf={
     *                                 @OA\Schema(type="string", example="Category meta description"),
     *                                 @OA\Schema(type="null", example=null)
     *                             }
     *                         ),
     *                         @OA\Property(property="image", type="string", example="https://example.com/uploads/categories/image.png"),
     *                         @OA\Property(property="top", type="integer", example=1),
     *                         @OA\Property(property="level", type="integer", example=1),
     *                         @OA\Property(property="parent_id", type="integer", example=0),
     *                         @OA\Property(
     *                             property="children",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=2),
     *                                 @OA\Property(property="name", type="string", example="Sneakers"),
     *                                 @OA\Property(property="slug", type="string", example="sneakers"),
     *                                 @OA\Property(
     *                                     property="meta_title",
     *                                     oneOf={
     *                                         @OA\Schema(type="string", example="Subcategory meta title"),
     *                                         @OA\Schema(type="null", example=null)
     *                                     }
     *                                 ),
     *                                 @OA\Property(
     *                                     property="meta_description",
     *                                     oneOf={
     *                                         @OA\Schema(type="string", example="Subcategory meta description"),
     *                                         @OA\Schema(type="null", example=null)
     *                                     }
     *                                 ),
     *                                 @OA\Property(property="level", type="integer", example=2),
     *                                 @OA\Property(property="parent_id", type="integer", example=1),
     *                                 @OA\Property(
     *                                     property="children",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=3),
     *                                         @OA\Property(property="name", type="string", example="Running Shoes"),
     *                                         @OA\Property(property="slug", type="string", example="running-shoes"),
     *                                         @OA\Property(
     *                                             property="meta_title",
     *                                             oneOf={
     *                                                 @OA\Schema(type="string", example="Subsubcategory meta title"),
     *                                                 @OA\Schema(type="null", example=null)
     *                                             }
     *                                         ),
     *                                         @OA\Property(
     *                                             property="meta_description",
     *                                             oneOf={
     *                                                 @OA\Schema(type="string", example="Subsubcategory meta description"),
     *                                                 @OA\Schema(type="null", example=null)
     *                                             }
     *                                         ),
     *                                         @OA\Property(property="level", type="integer", example=3),
     *                                         @OA\Property(property="parent_id", type="integer", example=2)
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="page", type="integer", example=1),
     *                     @OA\Property(property="page_size", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=100),
     *                     @OA\Property(property="last_page", type="integer", example=10),
     *                     @OA\Property(property="has_next", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $params = $this->buildParamsFromRequest($request);

        $data = $this->service->getList($request, $params);

        return $this->successResponse('', $data);
    }

    public function buildParamsFromRequest(Request $request)
    {
        $params = [];

        $params['page'] = $request->input('page');
        $params['per_page'] = $request->input('per_page');
        $params['show_level'] = $request->input('show_level', 1);
        if ($params['show_level'] < 1) {
            $params['show_level'] = 1;
        }

        return $params;
    }


    /**
     * @OA\Get(
     *     path="/api/mobile/v1/category/{id}",
     *     operationId="mobileCategoryDetail",
     *     tags={"Category"},
     *     summary="Get category detail",
     *     description="Returns detail of a specific category by ID and level",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="cate_level",
     *         in="query",
     *         required=true,
     *         description="Category level (1-3)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category detail retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Shoes"),
     *                 @OA\Property(property="is_age_restricted", type="boolean", example=false),
     *                 @OA\Property(property="slug", type="string", example="shoes"),
     *                 @OA\Property(property="banner", type="string", example="https://example.com/uploads/categories/banner.png"),
     *                 @OA\Property(property="icon", type="string", example="https://example.com/uploads/categories/icon.png"),
     *                 @OA\Property(
     *                     property="meta_title",
     *                     oneOf={
     *                         @OA\Schema(type="string", example="Category meta title"),
     *                         @OA\Schema(type="null", example=null)
     *                     }
     *                 ),
     *                 @OA\Property(
     *                     property="meta_description",
     *                     oneOf={
     *                         @OA\Schema(type="string", example="Category meta description"),
     *                         @OA\Schema(type="null", example=null)
     *                     }
     *                 ),
     *                 @OA\Property(property="image", type="string", example="https://example.com/uploads/categories/image.png"),
     *                 @OA\Property(property="top", type="integer", example=1),
     *                 @OA\Property(property="level", type="integer", example=1),
     *                 @OA\Property(property="parent_id", type="integer", example=0),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Invalid category level",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid category level"),
     *              @OA\Property(property="data", type="object", example={})
     *          )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */
    public function get(Request $request, $id)
    {
        $params['cate_level'] = $request->input('cate_level', 0);

        if ($params['cate_level'] == 0 || $params['cate_level'] > 3) {
            return $this->errorResponse('Invalid category level');
        }

        $data = $this->service->getByLevel($request, $id, $params);

        if (!$data) {
            return $this->errorResponse('Category not found', [], 404);
        }

        return $this->successResponse('', $data);
    }

}