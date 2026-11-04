<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\ContactFormRequest;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\ContactFormResource;
use App\Services\ContactFormService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Contact Form",
 *     description="Mobile API endpoints for contact form submissions"
 * )
 */
class ContactFormController extends Controller
{
    protected $contactFormService;

    public function __construct(ContactFormService $contactFormService)
    {
        $this->contactFormService = $contactFormService;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/contact-form",
     *     operationId="submitContactForm",
     *     tags={"Contact Form"},
     *     summary="Submit a contact form",
     *     description="Submit a new contact form from the mobile app",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","message"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="Sender name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Sender email"),
     *             @OA\Property(property="message", type="string", example="Hello, I have a question about your products.", description="Contact message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact form submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contact form submitted successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/ContactForm"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required.")),
     *                 @OA\Property(property="message", type="array", @OA\Items(type="string", example="The message field is required."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to submit contact form")
     *         )
     *     )
     * )
     */
    public function store(ContactFormRequest $request)
    {
        // Debug: Log the request data
        Log::info('ContactFormController store method', [
            'request_data' => $request->all(),
            'validated_data' => $request->validated()
        ]);
        
        try {
            $contactForm = $this->contactFormService->createContactForm($request->validated());

            return $this->successResponse(
                'Contact form submitted successfully',
                new ContactFormResource($contactForm),
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to submit contact form', [], 500);
        }
    }
}

