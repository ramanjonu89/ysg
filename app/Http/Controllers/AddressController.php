<?php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\{AddressService,
    CountryService,
    CityService,
    UserService
};
use App\User;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * @param AddressService
     */
    protected $addressService;

    /**
     * @param CountryService
     */
    protected $countryService;

    /**
     * @param CityService
     */
    protected $cityService;

    public function __construct(
        AddressService $addressService,
        CountryService $countryService,
        CityService $cityService
    ) {
        $this->addressService = $addressService;
        $this->countryService = $countryService;
        $this->cityService = $cityService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $valid = $this->validateRequest($request);

        if (!$valid['status']) {
            return response()->json([
                'status' => false,
                'errors' => $valid['errors']
            ], 422);
        }

        $data = $this->formatDataInputs($request);
        if ($request->has('customer_id')) {
            $data['user_id'] = $request->get('customer_id');
        }

        $address = $this->addressService->create($data);

        $userService = app()->make(UserService::class);
        $user = $userService->get($address->user_id);
        if ($user->addresses->count() == 0) {
            $address->set_default = 1;
        }

        $address->load(['cityRelationship', 'countryRelationship']);
        $html = view('frontend.partials.single_address', compact('address'))->render();
        $count = Address::where('user_id', $address->user_id)->count();

        return response()->json([
            'status'  => true,
            'html'    => $html,
            'count'   => $count,
            'message' => 'Address added successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $valid = $this->validateRequest($request);

            if (!$valid['status']) {
                return response()->json([
                    'status' => false,
                    'errors' => $valid['errors']
                ], 422);
            }

            $address = $this->addressService->getFirstWithConditions(['id' => $id]);

            if (!$address) {
                return response()->json([
                    'status' => false,
                    'message' => 'Address not found'
                ]);
            }

            $data = $this->formatDataInputs($request);

            if ($request->has('customer_id')) {
                $data['user_id'] = $request->get('customer_id');
            }
            $address = $this->addressService->update($address->id, $data);

            $address->load(['cityRelationship', 'countryRelationship']);
            $html = view('frontend.partials.single_address', compact('address'))->render();
            $count = Address::where('user_id', $address->user_id)->count();

            $deliveryAddress = Session::get('address');
            if ($deliveryAddress && $deliveryAddress->id == $address->id) {
                Session::put('address', $address);
            }

            return response()->json([
                'address_id'    => $address->id,
                'status'        => true,
                'html'          => $html,
                'count'         => $count,
                'message'       => 'Address updated successfully',
                'delivery_address'       => $deliveryAddress,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Sorry! Something went wrong'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $address = Address::findOrFail($id);

        if (in_array($user->type, [User::TYPE_CUSTOMER, User::TYPE_SELLER]) &&
            $user->id != $address->user_id)
        {
            flash(__('Sorry! Something went wrong'))->error();
            return back();
        }

        if (!$address->set_default) {
            $address->delete();

            flash(__('Address has been deleted successfully'))->success();
            return back();
        }
        flash(__('YSG requires an address for shipping and billing purposes'))->warning();

        return back();
    }

    public function set_default($id, $userId = null)
    {
        $user = Auth::user();
        if ($userId != null) {
            $userService = app()->make(UserService::class);
            $user = $userService->get($userId);
        }
        foreach ($user->addresses as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $address = Address::findOrFail($id);
        $address->set_default = 1;
        $address->save();

        return back();
    }

    public function listDeliveryAddresses(Request $request)
    {
        $user_id = Auth::user()->id;
        $selectedAddress = $request->get('address_id', '');
        $addresses = $this->addressService->getAllWithConditions(['user_id' => $user_id]);

        return view('frontend.inc.delivery.option_delivery_address', compact('addresses', 'selectedAddress'));
    }

    public function addDeliveryAddress(Request $request)
    {
        $valid = $this->validateRequest($request);
        if (!$valid['status']) {
            return response()->json([
                'status' => false,
                'errors' => $valid['errors']
            ], 422);
        }

        try {
            $data = $this->formatDataInputs($request);

            $address = $this->addressService->create($data);
            return response()->json([
                'status' => 1,
                'data' => $address,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Sorry! Something went wrong'
            ]);
        }
    }

    public function selectDeliveryAddress(Request $request)
    {
        $address = $this->addressService->getFirstWithConditions(['id' => $request->address_id]);
        Session::put('address', $address);
        return view('frontend.inc.delivery.address_selected', compact('address'));
    }

    private function formatDataInputs($request)
    {
        $data = [];

        $country = $this->countryService->get($request->country_id);
        $city    = $this->cityService->get($request->city_id);
        $data['country'] = $country->name;
        $data['city']    = $city->name;

        $data['address']       = $request->address . ' ' . $request->unitno;
        $data['country_id']    = $request->country_id;
        $data['city_id']       = $request->city_id;
        $data['postal_code']   = $request->postal_code;
        $data['country_code']  = $request->country_code;
        $data['phone']         = $request->phone;
        $data['title']         = $request->title;
        $data['user_id']       = Auth::user()->id;

        return $data;
    }

    private function validateRequest(Request $request)
    {
        $result = [
            'status' => true,
            'errors' => []
        ];

        $rules = [
            'address'       => 'required|string|max:255',
            'unitno'        => 'required|string|max:255',
            'postal_code'   => [
                'required',
                'regex:/^\d{6}$/',
            ],
            'country_id'    => 'required|integer',
            'city_id'       => 'required|integer',
            'country_code'  => 'required|integer',
            'phone'         => 'required|string'
        ];

        $messages = [
            'title.required'        => 'The title field is required.',
            'title.max'             => 'The title may not be greater than 255 characters.',
            'address.required'      => 'The address field is required.',
            'unitno.required'       => 'The unit number field is required.',
            'postal_code.required'  => 'The postal code field is required.',
            'postal_code.regex'     => 'The postal code must be 6 digits.',
            'country_id.required'   => 'The country field is required.',
            'city_id.required'      => 'The city field is required.',
            'country_code.required' => 'The country code field is required.',
            'phone.required'        => 'The phone number field is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->after(function ($validator) use ($request) {
            $country = $request->country_code;
            $phone   = $request->phone;

            if (!$country || !$phone) {
                return;
            }

            if ($country == Address::COUNTRY_CODE_SG) {
                if (!preg_match('/^[689]\d{7}$/', $phone)) {
                    $validator->errors()->add(
                        'phone',
                        'For Singapore (+65), the phone number must be 8 digits and start with 6, 8, or 9.'
                    );
                }
            } else {
                if (!preg_match('/^[0-9]{4,15}$/', $phone)) {
                    $validator->errors()->add(
                        'phone',
                        'The phone number must contain 4 to 15 digits for this country.'
                    );
                }
            }
        });

        if ($validator->fails()) {
            return [
                'status' => false,
                'errors' => $validator->errors()->toArray()
            ];
        }

        return $result;
    }
}
