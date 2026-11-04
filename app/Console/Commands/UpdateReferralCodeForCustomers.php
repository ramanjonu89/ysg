<?php

namespace App\Console\Commands;

use App\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateReferralCodeForCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:update_referral_code_for_customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update referral code for customers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            try {
                if (!isset($user->referral_code_register)) {
                    $user->referral_code_register = $user->id . '_' . Str::random(9);
                    $user->save();
                    $this->info('referral code of '. $user->id . ' update success');
                } else {
                    $this->info('referral code of ' . $user->id . ' already exists');  
                }
            } catch (Exception $e) {
                $this->error('referral code of ' . $user->id . ' update failed');
            }
        }
    }
}