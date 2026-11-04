<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Users\ChangePasswordRequest;
use App\Http\Requests\Api\Mobile\Users\UserRequest;
use App\Http\Resources\Mobile\UserResource;
use App\Services\Api\Mobile\UserOtpService;
use App\Services\Api\Mobile\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $service;
    protected $userOtpService;

    public function __construct(
        UserService $service,
        UserOtpService $userOtpService
    ) {
        $this->service      = $service;
        $this->userOtpService = $userOtpService;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/password/verify",
     *     tags={"User"},
     *     summary="Verify current password",
     *     description="Check if the provided password matches the authenticated user's current password.",
     *     operationId="verifyPassword",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password"},
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="MyCurrentPassword123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password verified successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not logged in",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity - Incorrect password",
     *         @OA\JsonContent(
     *             @OA\Property(property="err", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The provided password is incorrect.")
     *         )
     *     )
     * )
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        try {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return $this->errorResponse('The provided password is incorrect.');
            }

            return $this->successResponse('Password verified successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/change-password",
     *     tags={"User"},
     *     summary="Change user password",
     *     description="Allows an authenticated user to change their account password. The user must provide their current password for verification, along with a new password and confirmation. Password strength is enforced by validation rules (must include at least one uppercase letter, one lowercase letter, one number).",
     *     operationId="changePassword",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password", example="CurrentPass123"),
     *             @OA\Property(property="new_password", type="string", format="password", example="NewPass456"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="NewPass456")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password changed successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Password confirmation mismatch",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Password confirmation does not match.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User is not authenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Incorrect current password",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The current password is incorrect.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong. Please try again.")
     *         )
     *     )
     * )
     */

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = $request->user();
            $currentPassWord = $request->current_password;
            if ($currentPassWord && !Hash::check($currentPassWord, $user->password)) {
                return $this->errorResponse('The current password is incorrect.');
            }

            $this->service->updateUserPassword($user, [
                'password' => $request->new_password,
            ]);

            $this->service->sendMailChangePasswordNotification($user);

            return $this->successResponse('Password changed successfully.');
        } catch (\Exception $e) {
            Log::error('Error changing password: '. $e->getMessage());
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/mobile/v1/auth/user/destroy/{id}",
     *     summary="Delete an existing user",
     *     description="Remove a specific user by ID.",
     *     operationId="deleteUserById",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="The ID of the user to delete",
     *         @OA\Schema(
     *             type="integer",
     *             example=123
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully"),
     *             @OA\Property(property="user_id", type="integer", example=123),
     *             @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-08-26 20:15:30")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to delete this user.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $user = $this->service->getFirstWithConditions(['id' => $id]);

            if (!$user) {
                return $this->errorResponse('User not found.', [], 404);
            }

            if ($user->id !== Auth::user()->id) {
                return $this->errorResponse('You are not authorized to delete this user.', [], 403);
            }

            $emailNameOld  = explode('@', $user->email);
            $newEmail      = reset($emailNameOld) . '_deleted_' . time() . '@' . end($emailNameOld);

            $this->service->update($user->id, ['email' => $newEmail]);
            $this->service->destroy($user->id);
            return $this->successResponse('User deleted successfully.', [
                'user_id'    => $user->id,
                'deleted_at' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Sorry! Something went wrong', [], 500);
        }

        return $data;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/update-profile",
     *     operationId="updateUserProfile",
     *     tags={"User"},
     *     summary="Update detailed user profile information",
     *     description="Update the user's profile details such as name, contact information, and other personal data fields.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *             @OA\JsonContent(
     *                  required={"name", "email"},
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="country_code", type="string", example="+65"),
     *                  @OA\Property(property="phone", type="string", example="1234567890"),
     *                  @OA\Property(property="avatar_original", type="string", example="avatar.png", description="Profile photo file name"),
     *              )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User profile has been updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="country_code", type="string", example="+65"),
     *                 @OA\Property(property="phone", type="string", example="1234567890"),
     *                 @OA\Property(property="avatar_original", type="string", example="https://example.com/avatar.png"),
     *                 @OA\Property(property="is_update_email", type="boolean", description="Indicates if the email change requires verification"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"email": {"The email format is invalid."}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred. Please try again later.")
     *         )
     *     )
     * )
     */
    public function updateProfile(UserRequest $request)
    {
        try {
            $user = Auth::user();
            if ($request->filled('avatar_original')) {
                $avatarName = $request->input('avatar_original');
                $avatarPath = public_path(config('constant.user_avatar_path') . '/' . $avatarName);

                if (!File::exists($avatarPath)) {
                    return $this->errorResponse('The selected avatar does not exist.', [], 400);
                }
            }

            $data = $this->formatDataInputs($request);
            $user = $this->service->updateUserProfile($user, $data);

            return $this->successResponse('User profile has been updated successfully.', new UserResource($user));
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    private function formatDataInputs(Request $request)
    {
        $data = [];

        foreach (['name', 'phone', 'country_code'] as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if ($request->has('avatar_original')) {
            $avatarName = $request->input('avatar_original');
            $data['avatar_original'] = config('constant.user_avatar_path') . '/' . $avatarName;
        }

        return $data;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/auth/user/avatars",
     *     operationId="getListAvatars",
     *     summary="Retrieve list of available avatars",
     *     description="Returns a list of avatar files including their names and URLs.",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of avatars retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="avatar1.png"),
     *                     @OA\Property(property="url", type="string", example="https://example.com/assets/frontend/images/api/avatars/avatar1.png")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Avatar folder not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Avatar directory does not exist.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong. Please try again.")
     *         )
     *     )
     * )
     */
    public function getListAvatars()
    {
        try {
            $cacheKey = 'avatar_list';
            $path = public_path(config('constant.user_avatar_path'));

            if (!File::exists($path)) {
                return $this->errorResponse('Avatar directory does not exist.', [], 404);
            }

            $avatars = Cache::remember($cacheKey, 3600, function () use ($path) {
                $files = File::files($path);
                return collect($files)->map(function ($file) {
                    return [
                        'name' => basename($file),
                        'url'  => asset(config('constant.user_avatar_path') . '/' . basename($file))
                    ];
                })->toArray();
            });

            return $this->successResponse('Avatar list retrieved successfully.', $avatars);
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/send-otp-email-update",
     *     operationId="sendOtpForEmailUpdate",
     *     tags={"User"},
     *     summary="Send or resend OTP for updating user's email address",
     *     description="This endpoint handles both sending and resending a One-Time Password (OTP) to the user's new email address for verification before updating it in the profile. The system automatically determines whether to send a new OTP or resend an existing one, based on the user's OTP request history and resend limits.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"new_email"},
     *             @OA\Property(
     *                 property="new_email",
     *                 type="string",
     *                 format="email",
     *                 example="user@example.com",
     *                 description="The new email address that the OTP will be sent to."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP has been sent successfully to your new email address.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"new_email": {"The new email format is invalid or already in use."}}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="User is not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User is not authenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=429,
     *         description="Too many OTP requests or resend limit reached",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You have reached the maximum number of OTP resend attempts. Please try again later.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong. Please try again later.")
     *         )
     *     )
     * )
     */
    public function sendOtpForEmailUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_email' => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        try {
            $user = $request->user();
            $newEmail = $request->get('new_email');

            if ($user->email === $newEmail) {
                return $this->errorResponse('The new email format is invalid or already in use.', [], 422);
            }

            $userOtpExists = $this->userOtpService->getFirstWithConditions(['email' => $user->email]);
            $result  = $this->userOtpService->sendOtpUpdateMail($user, $userOtpExists, $newEmail);

            if (!$result['status']) {
                return $this->errorResponse($result['message'], [], 429);
            }

            return $this->successResponse($result['message']);

        } catch (\Exception $th) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/update-email",
     *     operationId="updateEmail",
     *     tags={"User"},
     *     summary="Verify OTP and update user's email address",
     *     description="Verify the OTP sent to the user's new email address. If valid and not expired, the user's profile email will be updated.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"otp"},
     *             @OA\Property(property="otp", type="string", example="123456", description="One-Time Password sent to new email address")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email Address Successfully Updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email Address Successfully Updated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The OTP is invalid or has expired.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong. Please try again.")
     *         )
     *     )
     * )
     */

    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        try {
            $user = $request->user();
            $userData = $this->userOtpService->getFirstWithConditions([
                'original_otp' => $request->otp,
                'email' => $user->email
            ]);

            if (!$userData) {
                return $this->errorResponse('Please check your Email Inbox for the One-Time Password (OTP). If you do not receive the One-Time Password Email, please click on the "Resend OTP" to receive a new One-Time Password Email', [], 400);
            }

            if (!$userData->isExpired()) {
                return $this->errorResponse('The OTP has expired', [], 400);
            }

            $details    = $userData->user_details ? json_decode($userData->user_details) : [];
            $newEmail   = isset($details->newEmail) ? $details->newEmail : null;

            $status = $this->service->update($user->id, ['email' => $newEmail]);
            $this->userOtpService->destroy($userData->id);

            return $this->successResponse('Email Address Successfully Updated');
        } catch (\Exception $th) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }
}
