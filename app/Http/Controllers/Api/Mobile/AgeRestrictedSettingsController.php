<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Services\AgeRestrictedSettingService;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Age Restricted",
 *     description="Mobile API Endpoints for Age Restricted Settings (18+ and 21+)"
 * )
 */
class AgeRestrictedSettingsController extends Controller
{

    protected $service;

    public function __construct(AgeRestrictedSettingService $service)
    {
        $this->service = $service;
    }
    /**
     * Get age restricted confirmation HTML content from database (18+ or 21+)
     *
     * @OA\Get(
     *     path="/api/mobile/v1/age-restricted/get-content",
     *     tags={"Age Restricted"},
     *     summary="Get Age Restricted Confirmation Content",
     *     description="Retrieve the HTML content for age restricted confirmation based on the specified age (18+ or 21+).",
     *
     *     @OA\Parameter(
     *         name="age",
     *         in="query",
     *         required=true,
     *         description="Restricted age value (only 18 or 21 allowed)",
     *         @OA\Schema(
     *             type="integer",
     *             example=18
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="age", type="integer", example=18),
     *             @OA\Property(property="top_content", type="string", example="<h1>Welcome</h1><p>Please confirm your age.</p>"),
     *             @OA\Property(
     *                 property="body_content",
     *                 type="string",
     *                 example="<h2>Confirm 18+</h2><p>You must be at least 18 years old.</p>"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Content not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Content not found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid age value",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Age must be 18 or 21")
     *         )
     *     )
     * )
     */
    public function getContent(Request $request)
    {
        try {
            $age = (int) $request->input('age');
            if (!in_array($age, [18, 21])) {
                return $this->errorResponse('Age must be 18 or 21', 422);
            }

            $content = $this->service->getFirstWithConditions(['age' => $age]);
            return $this->successResponse([
                'top_content' => $content ? $content->top_content : '',
                'body_content' => $content ? $content->body_content : ''
            ]);

        } catch (\Exception $e) {
            Log::error('Error get content age restricted: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while processing your request', 500);
        }
    }
}
