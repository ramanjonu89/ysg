<?php

namespace App\Http\Controllers;

use App\CorporateReferral;
use App\Coupon;
use App\Models\BusinessSetting;
use App\Repositories\CorporateReferralRepository;
use App\Repositories\UserCorporateAccessRepository;
use App\Repositories\UserRepository;
use App\SellerCorporateDomains;
use App\User;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReferralController extends Controller
{
    /**
     * @var UserCorporateAccessRepository
     */
    private $userCorporateAccessRepo;
    /**
     * @var UserRepository
     */
    private $userRepo;
    /**
     * @var CorporateReferralRepository
     */
    private $corporateReferralRepo;

    public function __construct(
        UserCorporateAccessRepository $userCorporateAccessRepo,
        CorporateReferralRepository $corporateReferralRepo,
        UserRepository $userRepo
    )
    {
        $this->userCorporateAccessRepo = $userCorporateAccessRepo;
        $this->userRepo = $userRepo;
        $this->corporateReferralRepo = $corporateReferralRepo;
    }

    /**
     *
     * @return Application|Factory|View
     */
    public function adminIndex()
    {
        $coupons = Coupon::select('id', 'code')->get();
        $referralEnabled = BusinessSetting::where('type', 'referral_enabled')->first();
        if ($referralEnabled) {
            $referralEnabled = $referralEnabled->value;
        }
        $recipientEnabled = BusinessSetting::where('type', 'referral_recipient_enabled')->first();
        if ($recipientEnabled) {
            $recipientEnabled = $recipientEnabled->value;
        }
        $senderEnabled = BusinessSetting::where('type', 'referral_sender_enabled')->first();
        if ($senderEnabled) {
            $senderEnabled = $senderEnabled->value;
        }
        $referralCouponId = '';
        $referralCoupon = BusinessSetting::where('type', 'referral_coupon_id')->first();
        if ($referralCoupon) {
            $referralCouponId = $referralCoupon->value;
        }
        $referralExclDomains = BusinessSetting::where('type', 'referral_exclude_domains')->first();
        $referralExclDomainArr = [];
        if ($referralExclDomains) {
            $referralExclDomains = $referralExclDomains->value;
            $referralExclDomainArr = $referralExclDomainArr ? json_decode($referralExclDomains, true) : [];
        }
        $corporateRefEnabled = BusinessSetting::where('type', 'corporate_referral_enabled')->first();
        if ($corporateRefEnabled) {
            $corporateRefEnabled = $corporateRefEnabled->value;
        }
        $referrerCouponId = '';
        $referrerCoupon = BusinessSetting::where('type', 'referrer_coupon_id')->first();
        if ($referrerCoupon) {
            $referrerCouponId = $referrerCoupon->value;
        }
        $corporateDomains = SellerCorporateDomains::select('domain')->get();

        $corporateRefs = CorporateReferral
            ::select(
                'domain',
                'share_limit',
            )
            ->get();

        return view('admin.referral.index', compact(
            'coupons',
            'referralEnabled',
            'referralCouponId',
            'referrerCouponId',
            'referralExclDomains',
            'referralExclDomainArr',
            'corporateDomains',
            'corporateRefEnabled',
            'corporateRefs',
            'recipientEnabled',
            'senderEnabled'
        ));
    }

    public function domainUserList(string $domain)
    {
        $users = $this->userRepo->getUsersByDomain($domain);
        $corporateReferral = $this->corporateReferralRepo->getByDomain($domain);
        $shareLimit = (int)$corporateReferral->share_limit;

        return view('admin.referral.domain_user', compact(['users', 'shareLimit']));
    }

    /**
     * @param Request $request
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            $refEnabled = $request->get('referral_enabled', false);
            $recipientEnabled = $request->get('referral_recipient_enabled', false);
            $senderEnabled = $request->get('referral_sender_enabled', false);
            $couponId = $request->get('referral_coupon_id', 0);
            $referrerCouponId = $request->get('referrer_coupon_id', 0);
            $excludeDomains = $request->get('referral_exclude_domains');
            $corporateRefEnabled = $request->get('corporate_ref_enabled', false);
            $corporateRefs = collect(json_decode($request->get('corporate_refs'), true));
            BusinessSetting::updateOrCreate([
                'type' => 'referral_enabled',
            ], [
                'value' => $refEnabled
            ]);
            BusinessSetting::updateOrCreate([
                'type' => 'referral_recipient_enabled',
            ], [
                'value' => $recipientEnabled
            ]);
            BusinessSetting::updateOrCreate([
                'type' => 'referral_sender_enabled',
            ], [
                'value' => $senderEnabled
            ]);
            BusinessSetting::updateOrCreate([
                'type' => 'referral_coupon_id',
            ], [
                'value' => $couponId
            ]);
            BusinessSetting::updateOrCreate([
                'type' => 'referrer_coupon_id',
            ], [
                'value' => $referrerCouponId
            ]);
            BusinessSetting::updateOrCreate([
                'type' => 'referral_exclude_domains',
            ], [
                'value' => $excludeDomains
            ]);
            BusinessSetting::updateOrCreate([
                'type' => 'corporate_referral_enabled',
            ], [
                'value' => $corporateRefEnabled
            ]);
            foreach ($corporateRefs as $corporateRef) {
                CorporateReferral::updateOrCreate([
                    'domain' => Arr::get($corporateRef, 'domain')
                ], $corporateRef);
            }
            DB::commit();
            flash(__('Referral settings has been updated successfully'))->success();

            return redirect(route('admin.referral.index'));
        } catch (Exception $e) {
            DB::rollBack();
            flash(__($e->getMessage()))->error();

            return back();
        }
    }

    public function domainReset(string $domain)
    {
        DB::beginTransaction();
        try {
            $this->userCorporateAccessRepo->resetAccessPassByDomain($domain);
            DB::commit();
            flash(__('Domain :domain has been reset successfully', ['domain' => $domain]))->success();

            return redirect(route('admin.referral.index'));
        } catch (Exception $e) {
            DB::rollBack();
            flash(__($e->getMessage()))->error();

            return back();
        }
    }

    public function userReset(User $user): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $this->userCorporateAccessRepo->resetAccessPassByUser($user);
            DB::commit();
            flash(__('User :user has been reset successfully', ['user' => $user->email]))->success();
        } catch (Exception $e) {
            DB::rollBack();
            flash(__($e->getMessage()))->error();
        }

        return back();
    }
}
