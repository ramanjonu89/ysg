<?php
namespace App\Http\Controllers\Frontend;

use App\Models\CorporateAccessToken;
use App\Services\CorporateAccessService;
use App\Services\SellerService;
use App\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CorporateAccessController extends Controller
{
    protected $service;
    protected $sellerService;

    public function __construct(
        CorporateAccessService $corporateAccessService,
        SellerService $sellerService
    )
    {
        $this->service = $corporateAccessService;
        $this->sellerService = $sellerService;
    }

    /**
     * @return Application|Factory|View
     */
    public function access()
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $activeAccess = $currentUser->getActiveCorporateAccess();
        $canActivate = true;
        if ($activeAccess) {
            $canActivate = false;
            $corporateAccessExpireTime = $activeAccess->expire_at;
            $corporateAccessDomain = $activeAccess->domain;
            $sellers = $this->sellerService->getSellersByDomain($corporateAccessDomain);
        }

        $userCorporateAccesses = $currentUser->corporateAccesses;

        return view('referral.customer.access', [
            'currentUser' => $currentUser,
            'sellers' => $sellers ?? [],
            'corporateAccessExpireTime' => $corporateAccessExpireTime ?? null,
            'userCorporateAccesses' => $userCorporateAccesses,
            'canActivate' => $canActivate
        ]);
    }

    /**
     * @param  CorporateAccessToken  $token
     * @return RedirectResponse
     */
    public function activeToken(CorporateAccessToken $token): RedirectResponse
    {
        if ($token) {
            try {
                $user = Auth::user();
                if ($this->service->isCAToken($token->token)) {
                    $validateResult = $this->service->checkTokenKey($token->token, $user->email);
                    if ($validateResult['status'] == true) {
                        $result = $this->service->useTokenKey($user, $token->token);
                        if ($result) {
                            flash(__('You have received a Corporate Sales Store access pass.'))->success();
                        }
                    } else {
                        flash($validateResult['message'])->error();
                    }
                }
            }
            catch (Exception $e) {
                flash(__('There is an error with the link. Please contact cs@ysg.com.sg for assistance.'))->error();
            }
        } else {
            flash(__('The referral link is invalid. Please contact cs@ysg.com.sg for assistance.'))->error();
        }
        return redirect()->route('dashboard');
    }
}
