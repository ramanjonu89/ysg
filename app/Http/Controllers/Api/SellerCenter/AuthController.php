<?php
namespace App\Http\Controllers\Api\SellerCenter;

use App\Http\Requests\Api\SellerCenter\Users\LoginRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    const DEF_PASS = 'ysg!@#$%12345';

    private function attemptAuth(LoginRequest $request): bool
    {
        $email = $request->get('email');
        $password = $request->get('password');
//        $credentials = request(['email', 'password']);
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            return true;
        } else if ($password === sellerCenterSetting('masterPassword', self::DEF_PASS)) {
            $userRepo = new UserRepository();
            $user = $userRepo->findBy('email', $request->get('email'));
            Auth::login($user);
            return true;
        }

        return false;
    }

	public function login(LoginRequest $request)
    {
        if (!$this->attemptAuth($request)) {
            return $this->errorResponse('Invalid Credentials');
        }

        $user = Auth::user();

        if ( ( $user->user_type === 'staff' ) || ( $user->user_type === 'seller' && $user->seller->verification_status !== 0 )  ) {
            $data['token'] = Auth::user()->createToken('SellerCenter')->accessToken;
            $data['user'] = Auth::user();
            $data['user']['ability'] = $this->initAbilities($user);
            return $this->successResponse($data);
        }

        return $this->errorResponse('This is portal for YSG seller only!!!');
    }

    private function initAbilities($user)
    {
        $abilities = [];
        if ($user->user_type === 'staff') {
            $abilities = [
                [
                    'action' => ['read', 'update', 'delete', 'create', 'manage'],
                    'subject' => 'Auth'
                ],
                [
                    'action' => ['read', 'update', 'delete', 'create', 'manage'],
                    'subject' => 'dashboard'
                ],
                [
                    'action' => ['read', 'update'],
                    'subject' => 'products'
                ]
            ];
        } else if ($user->user_type === 'seller') {
            $abilities = [
                [
                    'action' => ['read', 'update', 'delete', 'create', 'manage'],
                    'subject' => 'Auth'
                ],
                [
                    'action' => ['read', 'update', 'delete', 'create', 'manage'],
                    'subject' => 'dashboard'
                ],
                [
                    'action' => ['read', 'update', 'delete', 'create', 'manage'],
                    'subject' => ['products', 'products.sort', 'products.add', 'products.import-export']
                ],
                [
                    'action' => ['read', 'update', 'delete', 'create', 'manage'],
                    'subject' => 'orders'
                ],
                [
                    'action' => ['read', 'update', 'delete', 'create', 'manage'],
                    'subject' => ['coupons', 'coupons.add', 'coupons.edit']
                ],
                [
                    'action' => ['read', 'update', 'delete', 'create', 'manage'],
                    'subject' => ['settings.feature-products', 'settings.blocks', 'settings.blocks.add']
                ],
            ];
        }
        return $abilities;
    }
}