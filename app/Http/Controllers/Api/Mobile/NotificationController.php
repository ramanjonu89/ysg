<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Notifications",
 *     description="FCM token and push notification management"
 * )
 */
class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/notifications/token",
     *     summary="Store FCM token",
     *     description="Store or update FCM token for push notifications",
     *     operationId="storeNotificationToken",
     *     tags={"Notifications"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"fcm_token"},
     *             @OA\Property(property="fcm_token", type="string", example="fcm_token_string_here"),
     *             @OA\Property(property="device_id", type="string", example="uuid-device-id"),
     *             @OA\Property(property="device_type", type="string", enum={"ios", "android"}, example="android"),
     *             @OA\Property(property="device_model", type="string", example="iPhone 14 Pro"),
     *             @OA\Property(property="os_version", type="string", example="iOS 16.0"),
     *             @OA\Property(property="app_version", type="string", example="1.0.0")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FCM token saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="FCM token saved successfully"),
     *             @OA\Property(property="device_id", type="string", example="uuid-device-id")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function storeToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'device_id' => 'nullable|string',
            'device_type' => 'nullable|string|in:ios,android',
            'device_model' => 'nullable|string',
            'os_version' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('api')->user();
        $deviceId = $request->device_id ?? ($request->device_id ?? Str::uuid()->toString());

        $existingToken = FcmToken::where('fcm_token', $request->fcm_token)->first();

        if ($existingToken) {
            $existingToken->update([
                'user_id' => $user ? $user->id : null,
                'device_id' => $deviceId,
                'device_type' => $request->device_type ?? 'android',
                'device_model' => $request->device_model,
                'os_version' => $request->os_version,
                'app_version' => $request->app_version,
                'is_active' => true,
            ]);
        } else {
            FcmToken::create([
                'user_id' => $user ? $user->id : null,
                'device_id' => $deviceId,
                'fcm_token' => $request->fcm_token,
                'device_type' => $request->device_type ?? 'android',
                'device_model' => $request->device_model,
                'os_version' => $request->os_version,
                'app_version' => $request->app_version,
                'is_active' => true,
            ]);
        }

        if ($user) {
            FcmToken::where('user_id', $user->id)
                ->where('fcm_token', '!=', $request->fcm_token)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FCM token saved successfully',
            'device_id' => $deviceId
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/mobile/v1/notifications/token",
     *     summary="Remove FCM token",
     *     description="Remove or deactivate FCM token",
     *     operationId="removeNotificationToken",
     *     tags={"Notifications"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="fcm_token", type="string", example="fcm_token_string_here"),
     *             @OA\Property(property="device_id", type="string", example="uuid-device-id")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FCM token removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="FCM token removed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function removeToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required_without:device_id|string',
            'device_id' => 'required_without:fcm_token|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = FcmToken::query();

        if ($request->has('fcm_token')) {
            $query->where('fcm_token', $request->fcm_token);
        }

        if ($request->has('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        $user = Auth::guard('api')->user();
        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->whereNull('user_id');
        }

        $query->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token removed successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/notifications/send-test",
     *     summary="Send test notification",
     *     description="Send a test push notification to authenticated user",
     *     operationId="sendTestNotification",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Test Notification"),
     *             @OA\Property(property="body", type="string", example="This is a test notification"),
     *             @OA\Property(property="data", type="object", example={"key": "value"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification sent result",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Notification sent successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication required",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Authentication required")
     *         )
     *     )
     * )
     */
    public function sendTest(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $title = $request->input('title', 'Test Notification');
        $body = $request->input('body', 'This is a test notification');
        $data = $request->input('data', []);

        $result = $this->firebaseService->sendToUser($user, $title, $body, $data);

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Notification sent successfully' : 'Failed to send notification'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/notifications/send-to-device",
     *     summary="Send notification to device",
     *     description="Send push notification to specific device by device ID",
     *     operationId="sendNotificationToDevice",
     *     tags={"Notifications"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"device_id", "title", "body"},
     *             @OA\Property(property="device_id", type="string", example="uuid-device-id"),
     *             @OA\Property(property="title", type="string", example="New Promotion"),
     *             @OA\Property(property="body", type="string", example="Check out our latest deals!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 example={"type": "promotion", "promotion_id": "123"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Notification sent successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Device not found or not active")
     *         )
     *     )
     * )
     */
    public function sendToDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $deviceId = $request->device_id;
        $title = $request->title;
        $body = $request->body;
        $data = $request->input('data', []);

        $result = $this->firebaseService->sendToAnonymousUsers([$deviceId], $title, $body, $data);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found or not active'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully'
        ]);
    }
}
