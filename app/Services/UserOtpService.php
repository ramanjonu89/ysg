<?php
namespace App\Services;

use App\Models\Address;
use App\Models\UserOtp;
use App\User;
use App\Repositories\UserOtpRepository;

class UserOtpService extends AbstractService
{
    protected $repo;
    public function __construct(UserOtpRepository $repo)
    {
        return parent::__construct($repo);
    }

    public function getList($params = [])
    {
        return $this->repo->getList($params);
    }

    public function formatDataInputsRegister($request) {
        $data = [];
        $userDetails = json_decode($request->user_details);
        $data['name']                  = $userDetails->name;
        $data['email']                 = $userDetails->email;
        $data['password']              = $userDetails->password;
        $data['ref']                   = isset($userDetails->ref) ? $userDetails->ref : null;

        if ($request->user_type === User::TYPE_SELLER) {
            $data['phone']        = $userDetails->phone;
            $data['website']      = $userDetails->website;
            $data['company_name'] = $userDetails->company_name;
            $data['user_type']    = User::TYPE_SELLER;
        } else {
            $data['ref']          = isset($userDetails->ref) ? $userDetails->ref : null;
            $data['user_type']    = User::TYPE_CUSTOMER;
            if (isset($userDetails->referral_by)) {
                $data['referral_by']  = $userDetails->referral_by;
            }
            $data['country_code'] = isset($userDetails->country_code) ? $userDetails->country_code : Address::COUNTRY_CODE_SG;
            $data['phone']        = isset($userDetails->phone) ? $userDetails->phone : '';
            $data['receive_special_offers'] = $userDetails->receive_special_offers ?? 0;
        }

        return $data;
    }
}