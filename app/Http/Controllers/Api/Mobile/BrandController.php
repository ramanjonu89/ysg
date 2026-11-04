<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\Mobile\BrandCollection;
use Illuminate\Http\Request;
use App\Services\Api\Mobile\BrandService;

/**
 * @OA\Tag(
 *     name="Brand",
 *     description="Mobile API endpoints for brand management"
 * )
 */
class BrandController extends Controller
{
    protected $service;
    public function __construct(BrandService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/brands",
     *     operationId="mobileBrandList",
     *     tags={"Brand"},
     *     summary="Get paginated list of brands",
     *     description="Returns a paginated list of brands for mobile users",
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
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand list retrieved successfully",
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
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="name", type="string", example="Nike"),
     *                          @OA\Property(property="logo", type="string", example="https://example.com/uploads/brands/apple-logo.png"),
     *                          @OA\Property(property="top", type="integer", example=1),
     *                          @OA\Property(property="slug", type="string", example="abc"),
     *                          @OA\Property(
     *                              property="meta_title",
     *                              oneOf={
     *                                  @OA\Schema(type="string", example="Brand meta title"),
     *                                  @OA\Schema(type="null", example=null)
     *                              }
     *                          ),
     *                          @OA\Property(
     *                               property="meta_description",
     *                               oneOf={
     *                                   @OA\Schema(type="string", example="Brand meta description"),
     *                                   @OA\Schema(type="null", example=null)
     *                               }
     *                           ),
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
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $params = $this->buildParamsFromRequest($request);

        $brands = $this->service->getList($params);

        $data = $this->buildResponseFromPagination($brands);

        return $this->successResponse('', $data);
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/brand/{id}",
     *     operationId="mobileBrandDetail",
     *     tags={"Brand"},
     *     summary="Get brand detail",
     *     description="Returns detail of a specific brand by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Brand ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand detail retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Nike"),
     *                 @OA\Property(property="logo", type="string", example="https://example.com/uploads/brands/apple-logo.png"),
     *                 @OA\Property(property="top", type="integer", example=1),
     *                 @OA\Property(property="slug", type="string", example="abc"),
     *                 @OA\Property(
     *                     property="meta_title",
     *                     oneOf={
     *                         @OA\Schema(type="string", example="Brand meta title"),
     *                         @OA\Schema(type="null", example=null)
     *                     }
     *                 ),
     *                 @OA\Property(
     *                     property="meta_description",
     *                     oneOf={
     *                         @OA\Schema(type="string", example="Brand meta description"),
     *                         @OA\Schema(type="null", example=null)
     *                     }
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Brand not found"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */
    public function get($id)
    {
        $brand = $this->service->get($id);
        if (!$brand) {
            return $this->errorResponse('Brand not found', [], 404);
        }

        $data = new BrandCollection($brand);
        return $this->successResponse('', $data);
    }

    public function buildResponseFromPagination($brands): array
    {
        return [
            'items' => BrandCollection::collection($brands->items()),
            'pagination' => [
                'page'       => $brands->currentPage(),
                'page_size'  => $brands->perPage(),
                'total'      => $brands->total(),
                'last_page'  => $brands->lastPage(),
                'has_next'   => $brands->hasMorePages(),
            ]
        ];
    }

    public function buildParamsFromRequest(Request $request)
    {
        $params = [];

        $params['page'] = $request->input('page');
        $params['per_page'] = $request->input('per_page');

        return $params;
    }
} 