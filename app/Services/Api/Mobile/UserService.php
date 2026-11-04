<?php
namespace App\Services\Api\Mobile;

use App\Repositories\Api\Mobile\UserRepository;
use App\Services\AbstractService;
use Illuminate\Support\Facades\Hash;
use App\Services\UserMailService;

class UserService extends AbstractService
{
    protected $repo;
    protected $mailService;

    public function __construct(UserRepository $repo, UserMailService $mailService)
    {
        parent::__construct($repo);
        $this->mailService = $mailService;
    }

    /**
     * Update user profile
     *
     * @param  mixed  $user
     * @param  array  $data
     * @return mixed
     */
    public function updateUserProfile($user, $data)
    {

        // if (!empty($data['email']) && $data['email'] !== $user->email) {
        //     $newEmail = $data['email'];
        //     $this->mailService->sendUpdateEmailVerificationMailFromMobile($user, $newEmail);
        //     $isUpdateEmail = true;
        // }

        $user = $this->repo->update($user->id, $data);

        return $user;
    }

    /**
     * Update user password
     *
     * @param  mixed  $user
     * @param  array  $data
     * @return mixed
     */
    public function updateUserPassword($user, $data)
    {
        $user = $this->repo->update($user->id, [
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }
    
    public function sendMailChangePasswordNotification($user)
    {
        $this->mailService->sendChangePasswordNotificationMail($user);
    }
}