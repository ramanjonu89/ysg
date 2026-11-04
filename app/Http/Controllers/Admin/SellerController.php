<?php
namespace App\Http\Controllers\Admin;

use App\Order;
use App\OrderDetail;
use App\Product;
use App\Models\Seller;
use App\Services\{AttributeService,
    SellerService,
    UserOtpService,
    UserService
};
use App\Services\CustomPage\BlockService;
use App\Shop;
use App\User;
use App\Http\Controllers\Controller;
use App\Jobs\SyncSellerJob;
use App\Excel\SellerExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class SellerController extends Controller
{
    /**
     * @param SellerService
     */
    protected $sellerService;

    /**
     * @param UserOtpService
     */
    protected $userOtpService;

    /**
     * @param UserService
     */
    protected $userService;

    public function __construct(
        SellerService  $sellerService,
        UserOtpService $userOtpService,
        UserService    $userService
    ) {
        $this->sellerService  = $sellerService;
        $this->userOtpService = $userOtpService;
        $this->userService    = $userService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, SellerService $sellerService)
    {
        $sort_search = $request->search;
        $approved = $request->approved_status;
        $sort_by = $request->sort_by ?? 'index';
        $sort_direction = $request->sort_direction ?? 'asc';

        $params = [
            'search' => $request->search,
            'verification_status' => $request->approved_status,
            'sort_by' => $sort_by,
            'sort_direction' => $sort_direction,
        ];

        $sellers = $sellerService->getList($params);

        return view('sellers.index', compact('sellers', 'sort_search', 'approved', 'sort_by', 'sort_direction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sellers.create');
    }

    private function storeImage($image) {
        $image_file_name = time().rand(11111,99999).$image->getClientOriginalName();
        $block_content_image_path = config('constant.seller_image_path');
        if (!File::isDirectory($block_content_image_path)) {
            File::makeDirectory($block_content_image_path, 0775, true);
        }
        $image->move($block_content_image_path, $image_file_name);
        return '/' . $block_content_image_path . '/' . $image_file_name;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (User::where('email', $request->email)->first() != null) {
            flash(__('Email already exists!'))->error();

            return back();
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->company_id = $request->company_id;
        $user->user_type = "seller";
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->password = Hash::make($request->password);
        if ($user->save()) {
            $seller = new Seller;
            $seller->user_id = $user->id;
            if (isset($request->image)) {
                $seller->image = $this->storeImage($request->image);
            }
            if ($seller->save()) {
                $shop = new Shop;
                $shop->user_id = $user->id;
                $shop->slug = 'demo-shop-' . $user->id;
                $shop->save();
                flash(__('Seller has been inserted successfully'))->success();

                return redirect()->route('sellers.index');
            }
        }
        flash(__('Something went wrong'))->error();

        return back();
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
     */
    public function edit($id, BlockService $blockService, AttributeService $attributeService)
    {
        $seller = Seller::findOrFail(decrypt($id));
        $shop = $seller->user->shop;
        $selectedAttributes = isset($shop) ? $shop->allowedAttributes->pluck('id')->toArray() : [];
        $addresses = $seller->user->addresses;
        $oldCorporateDomains = $seller->corporate_domains()->get();
        $emailCoporateAcces = $seller->corporate_emails()->get();

        $blocks = $shop->blocks??[];
        $allBlocks = $blockService->getBlockListForShop();
        $blockTypes = $blockService->getShopListType();

        $attributes = $attributeService->getAll();

        return view('admin.seller.edit', compact(
            'seller',
            'oldCorporateDomains',
            'shop',
            'addresses',
            'blocks',
            'allBlocks',
            'blockTypes',
            'attributes',
            'selectedAttributes',
            'emailCoporateAcces',
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->sellerService->updateByRequest($id, $request);
            flash(__('Seller has been updated successfully'))->success();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $seller = Seller::findOrFail($id);
            Shop::where('user_id', $seller->user->id)->delete();
            Product::where('user_id', $seller->user->id)->delete();
            Order::where('user_id', $seller->user->id)->delete();
            OrderDetail::where('seller_id', $seller->user->id)->delete();
            $originalEmail = $seller->user->email;
            $emailNameOld  = explode('@', $originalEmail);
            $newEmail      = reset($emailNameOld).'_deleted_'.time().'@'.end($emailNameOld);

            $this->userService->update($seller->user->id, ['email' => $newEmail]);
            $this->userService->destroy($seller->user->id);
            $this->sellerService->destroy($id);
            DB::commit();
            flash(__('Seller has been deleted successfully'))->success();
            return redirect()->route('sellers.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function show_verification_request($id)
    {
        $seller = Seller::findOrFail($id);

        return view('sellers.verification', compact('seller'));
    }

    public function approve_seller($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->verification_status = 1;
        if ($seller->save()) {
            flash(__('Seller has been approved successfully'))->success();

            return redirect()->route('sellers.index');
        }
        flash(__('Something went wrong'))->error();

        return back();
    }

    public function reject_seller($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->verification_status = 0;
        $seller->verification_info = null;
        if ($seller->save()) {
            flash(__('Seller verification request has been rejected successfully'))->success();

            return redirect()->route('sellers.index');
        }
        flash(__('Something went wrong'))->error();

        return back();
    }

    // sync data here
    public function sync(User $user)
    {
        SyncSellerJob::dispatch($user);
        flash(translate('OMS sync successfully'))->success();
        return redirect()->route('sellers.index');
    }

    public function payment_modal(Request $request)
    {
        $seller = Seller::findOrFail($request->id);

        return view('sellers.payment_modal', compact('seller'));
    }

    public function profile_modal(Request $request)
    {
        $seller = Seller::findOrFail($request->id);

        return view('sellers.profile_modal', compact('seller'));
    }

    public function updateApproved(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        $seller->verification_status = $request->status;
        if ($seller->save()) {
            return 1;
        }

        return 0;
    }

    public function login($id)
    {
        $seller = Seller::findOrFail(decrypt($id));
        $user = $seller->user;
        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id)
    {
        $seller = Seller::findOrFail($id);
        $user = $seller->user;

        $user->banned = !$user->banned;
        $user->save();

        return back();
    }

    public function exportAll(Request $request)
    {
        $params['keyword'] = $request->get('keyword', '');
        $params['verification_status'] = $request->get('approved_status', '');

        return Excel::download(new SellerExport($params), 'sellers_export.xlsx');
    }

}

