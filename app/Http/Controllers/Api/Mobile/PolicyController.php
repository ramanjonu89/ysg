<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Mobile\PolicyResource;
use App\Models\Policy;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Policies",
 *     description="Mobile API endpoints for policy content"
 * )
 */
class PolicyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/policy",
     *     operationId="getPolicyContent",
     *     tags={"Policies"},
     *     summary="Get policy content by name",
     *     description="Retrieve policy content by policy name. Common policy names include: return_policy, support_policy, terms, privacy_policy, etc.",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="Policy name (e.g., return_policy, support_policy, terms, privacy_policy, seller_policy)",
     *         @OA\Schema(
     *             type="string",
     *             example="return_policy"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Policy retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Policy retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Policy")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Missing policy name",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Policy name is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Policy not found")
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
    public function show(Request $request)
    {
        try {
            $policyName = $request->query('name');

            if (!$policyName) {
                return $this->errorResponse('Policy name is required', [], 400);
            }

            $policy = Policy::where('name', $policyName)->first();

            if (!$policy) {
                return $this->errorResponse('Policy not found', [], 404);
            }

            return $this->successResponse('Policy retrieved successfully', new PolicyResource($policy));

        } catch (\Exception $e) {
            return $this->errorResponse('Internal server error: ' . $e->getMessage(), [], 500);
        }
    }
}

