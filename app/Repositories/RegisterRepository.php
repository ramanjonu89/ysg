<?php
namespace App\Repositories;

use App\Addon;
use App\User;
use Exception;
use App\Models\Customer;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\OTPVerificationController;

class RegisterRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return User::class;
    }

    public function createAccount(array $data)
    {
        $user = null;
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            try {
                DB::beginTransaction();

                $user = $this->model->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'country_code' => $data['country_code'],
                    'phone' => $data['phone'],
                    'receive_special_offers' => $data['receive_special_offers'],
                ]);
                $user->referral_code_register = $user->id . '_' . Str::random(9);
                $user->save();

                $customer = new Customer();
                $customer->user_id = $user->id;
                $customer->save();

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

            }
        } else {
            if (Addon::where('unique_identifier', 'otp_system')->first() != null && Addon::where('unique_identifier', 'otp_system')->first()->activated) {
                try {
                    DB::beginTransaction();

                    $user = $this->model->create([
                        'name' => $data['name'],
                        'country_code' => $data['country_code'],
                        'phone' => $data['phone'],
                        'password' => $data['password'],
                        'verification_code' => rand(100000, 999999),
                        'receive_special_offers' => $data['receive_special_offers'],
                    ]);
                    $user->referral_code_register = $user->id . '_' . Str::random(9);
                    $user->save();

                    $customer = new Customer;
                    $customer->user_id = $user->id;
                    $customer->save();
                    // $otpController = new OTPVerificationController;
                    // $otpController->send_code($user);

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                }
            }
        }
        if ($user && Cookie::has('referral_code')) {
            $referral_code = Cookie::get('referral_code');
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if ($referred_by_user != null) {
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }

        return $user;
    }

    public function activeByReferralCode($referralBy, $user)
    {
        if (isset($referralBy)) {
            $referred_by_user = User::where('referral_code_register', $referralBy)->first();
            if ($referred_by_user) {
                $user->introduced_by = $referred_by_user->referral_code_register . '-' . $referred_by_user->email;
                $user->save();
                return true;
            }

            return false;
        }

        return false;
    }
}
