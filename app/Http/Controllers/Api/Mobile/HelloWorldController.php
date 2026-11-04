<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Mobile Hello World",
 *     description="Mobile Hello World API endpoints"
 * )
 */
class HelloWorldController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/hello-world",
     *     operationId="mobileHelloWorld",
     *     tags={"Mobile Hello World"},
     *     summary="Get Hello World message",
     *     description="Returns a simple Hello World message for authenticated mobile users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Hello World message retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hello World message retrieved"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Hello World from Mobile API!"),
     *                 @OA\Property(property="timestamp", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
     *                 @OA\Property(property="user_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $data = [
            'message' => 'Hello World from Mobile API!',
            'timestamp' => now()->toISOString(),
            'user_id' => $request->user()->id
        ];

        return $this->successResponse('Hello World message retrieved', $data);
    }
} 