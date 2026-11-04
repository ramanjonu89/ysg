<?php
namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Users\AddressRequest;
use App\Http\Resources\Mobile\AddressResource;
use App\Http\Resources\Mobile\CityResource;
use App\Http\Resources\Mobile\CountryResource;
use App\Models\Address;
use App\Models\Country;
use App\Services\Api\Mobile\AddressService;
use App\Services\Api\Mobile\CityService;
use App\Services\Api\Mobile\CountryService;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    protected $service;
    protected $cityService;
    protected $countryService;

    public function __construct(
        AddressService $service,
        CityService $cityService,
        CountryService $countryService
    ) {
        $this->service  = $service;
        $this->cityService = $cityService;
        $this->countryService = $countryService;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/auth/user/addresses",
     *     operationId="mobileAddressesList",
     *     tags={"User Address"},
     *     summary="Get list of addresses for authenticated user",
     *     description="Returns a list of addresses for mobile users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Address list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Address list retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="user_id", type="integer", example=10),
     *                      @OA\Property(property="address", type="string", example="10 Bayfront Avenue"),
     *                      @OA\Property(property="country", type="string", example="Singapore"),
     *                      @OA\Property(property="city", type="string", example="Singapore"),
     *                      @OA\Property(property="postal_code", type="string", example="018956"),
     *                      @OA\Property(property="country_code", type="string", example="+65"),
     *                      @OA\Property(property="phone", type="string", example="81234567"),
     *                      @OA\Property(property="title", type="string", example="675, Avive Street, Singapore"),
     *                      @OA\Property(property="default", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
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
    public function getAddresses()
    {
        try {
            $user = Auth::user();
            $addresses = $user->addresses;
            return $this->successResponse('Address list retrieved successfully.', AddressResource::collection($addresses));
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/address",
     *     summary="Create new address",
     *     description="Create a new address for the authenticated user.",
     *     operationId="createAddress",
     *     tags={"User Address"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"address","country","city","postal_code","phone"},
     *             @OA\Property(property="address", type="string", example="789 Clementi Road, Singapore"),
     *             @OA\Property(property="country_id", type="integer", example="195"),
     *             @OA\Property(property="city_id", type="integer", example="98"),
     *             @OA\Property(property="postal_code", type="string", example="240000"),
     *             @OA\Property(property="country_code", type="string", example="+65"),
     *             @OA\Property(property="phone", type="string", example="61230000"),
     *             @OA\Property(property="set_default", type="boolean", example=false),
     *             @OA\Property(property="title", type="string", example="675, Avive Street, Singapore")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Address created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Address created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2001),
     *                 @OA\Property(property="user_id", type="integer", example=10),
     *                 @OA\Property(property="address", type="string", example="789 Clementi Road, Singapore"),
     *                 @OA\Property(property="country", type="string", example="Singapore"),
     *                 @OA\Property(property="city", type="string", example="Singapore"),
     *                 @OA\Property(property="postal_code", type="string", example="240000"),
     *                 @OA\Property(property="country_code", type="string", example="+65"),
     *                 @OA\Property(property="phone", type="string", example="61230000"),
     *                 @OA\Property(property="title", type="string", example="675, Avive Street, Singapore"),
     *                 @OA\Property(property="default", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"address": {"The address field is required."}}
     *             )
     *         )
     *     )
     * )
     */
    public function addAddress(AddressRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $this->formatDataInputs($request);
            $data['user_id'] = $user->id;

            if ($data['set_default']) {
                $this->service->resetDefaultAddress($user->id, $data['set_default']);
            }

            $status = $this->service->create($data);
            $addresses = $user->addresses;
            return $this->successResponse('Address has been inserted successfully.', AddressResource::collection($addresses));
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\post(
     *     path="/api/mobile/v1/auth/user/address/{address}/update",
     *     summary="Update address",
     *     description="Update a specific address of the authenticated user.",
     *     operationId="updateAddress",
     *     tags={"User Address"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="address",
     *         in="path",
     *         required=true,
     *         description="ID of the address to update",
     *         @OA\Schema(type="integer", example=1820)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="address", type="string", example="456 New Street, Singapore"),
     *             @OA\Property(property="country_id", type="integer", example="195"),
     *             @OA\Property(property="city_id", type="integer", example="98"),
     *             @OA\Property(property="postal_code", type="string", example="239999"),
     *             @OA\Property(property="country_code", type="string", example="+65"),
     *             @OA\Property(property="phone", type="string", example="64567890"),
     *             @OA\Property(property="set_default", type="boolean", example=false),
     *             @OA\Property(property="title", type="string", example="675, Avive Street, Singapore")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Address updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Address updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1820),
     *                 @OA\Property(property="user_id", type="integer", example=10),
     *                 @OA\Property(property="address", type="string", example="456 New Street, Singapore"),
     *                 @OA\Property(property="country", type="string", example="Singapore"),
     *                 @OA\Property(property="city", type="string", example="Singapore"),
     *                 @OA\Property(property="postal_code", type="string", example="239999"),
     *                 @OA\Property(property="country_code", type="string", example="+65"),
     *                 @OA\Property(property="phone", type="string", example="64567890"),
     *                 @OA\Property(property="title", type="string", example="675, Avive Street, Singapore"),
     *                 @OA\Property(property="default", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Address not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Address not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Address does not belong to user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You do not have permission to modify this address.")
     *         )
     *     )
     * )
     */
    public function update(AddressRequest $request, Address $address)
    {
        try {
            $user = Auth::user();
            if (!$address) {
                return $this->errorResponse('Address not found.', [], 404);
            }

            if ($address->user_id !== $user->id) {
                return $this->errorResponse('You are not authorized to update this address.', [], 403);
            }

            $data = $this->formatDataInputs($request);
            if ($data['set_default']) {
                $this->service->resetDefaultAddress($user->id);
            }

            $status = $this->service->update($address->id, $data);
            $addresses = $user->addresses;
            return $this->successResponse('Address updated successfully.', AddressResource::collection($addresses));
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    public function formatDataInputs($request)
    {
        $data = $request->only(['address', 'country_id', 'city_id', 'postal_code', 'country_code', 'phone', 'set_default', 'title']);
        $data['set_default'] = isset($data['set_default']) ? (bool)$data['set_default'] : false;

        $country = $this->countryService->getFirstWithConditions(['id' => $data['country_id']]);
        $city = $this->cityService->getFirstWithConditions(['id' => $data['city_id']]);

        $data['country'] = $country ? $country->name : null;
        $data['city'] = $city ? $city->name : null;

        return $data;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/user/address/{address}/set-default",
     *     summary="Set default address",
     *     description="Set a specific address as the default for the authenticated user.",
     *     operationId="setDefaultAddress",
     *     tags={"User Address"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="address",
     *         in="path",
     *         required=true,
     *         description="ID of the address to set as default",
     *         @OA\Schema(type="integer", example=1820)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Address set as default successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Address set as default successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=10),
     *                 @OA\Property(property="address", type="string", example="123 Orchard Road, Singapore"),
     *                 @OA\Property(property="country", type="string", example="Singapore"),
     *                 @OA\Property(property="city", type="string", example="Singapore"),
     *                 @OA\Property(property="postal_code", type="string", example="238888"),
     *                 @OA\Property(property="country_code", type="string", example="+65"),
     *                 @OA\Property(property="phone", type="string", example="61234567"),
     *                 @OA\Property(property="set_default", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Address not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Address not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Address does not belong to user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You do not have permission to modify this address.")
     *         )
     *     )
     * )
     */

    public function setDefault(Address $address)
    {
        try {
            $user = Auth::user();
            if (!$address) {
                return $this->errorResponse('Address not found.', [], 404);
            }

            if ($address->user_id !== $user->id) {
                return $this->errorResponse('You are not authorized to set this address as default.', [], 403);
            }

            $status = $this->service->resetDefaultAddress($user->id);
            $address = $this->service->update($address->id, ['set_default' => true]);

            return $this->successResponse('Address set as default successfully.', new AddressResource($address));
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/mobile/v1/auth/user/address/{address}/destroy",
     *     summary="Delete an existing address",
     *     description="Remove a specific address of the authenticated user.",
     *     operationId="deleteUserAddress",
     *     tags={"User Address"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="address",
     *         in="path",
     *         description="The ID of the address to delete",
     *         required=true,
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Address deleted successfully (no content returned)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Address not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function destroy(Address $address)
    {
        try {
            $user = Auth::user();
            if (!$address) {
                return $this->errorResponse('Address not found.', [], 404);
            }

            if ($address->user_id !== $user->id) {
                return $this->errorResponse('You are not authorized to delete this address.', [], 403);
            }

            $this->service->destroy($address->id);
            return $this->successResponse('Address deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/user/address/countries",
     *     operationId="mobileAddressCountriesList",
     *     tags={"User Address"},
     *     summary="Get list of address countries for authenticated user",
     *     description="Returns a list of address countries for mobile users",
     *     @OA\Response(
     *         response=200,
     *         description="Countries list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Countries list retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Singapore"),
     *                      @OA\Property(property="country_code", type="string", example="+65")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
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

    public function getCountries()
    {
        try {
            $countries = $this->countryService->getList(['status' => Country::STATUS_ACTIVE]);
            return $this->successResponse('Countries list retrieved successfully.', CountryResource::collection($countries));
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/auth/user/address/cities/{country_id}",
     *     operationId="mobileAddressCitiesList",
     *     tags={"User Address"},
     *     summary="Get list of cities by country id",
     *     description="Returns a list of cities that belong to a given country.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="country_id",
     *         in="path",
     *         required=true,
     *         description="ID of the country to fetch cities for",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cities list retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cities list retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=101),
     *                     @OA\Property(property="name", type="string", example="Singapore"),
     *                     @OA\Property(property="country_id", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Country or cities not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Country not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred. Please try again later.")
     *         )
     *     )
     * )
     */
    public function getCities($country_id)
    {
        try {
            $cities = $this->cityService->getAllWithConditions([
                'country_id' => $country_id,
            ]);

            return $this->successResponse('Cities list retrieved successfully.', CityResource::collection($cities));
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

}
