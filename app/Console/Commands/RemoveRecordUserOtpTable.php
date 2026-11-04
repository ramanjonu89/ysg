<?php

namespace App\Console\Commands;

use App\Models\UserOtp;
use App\Services\UserOtpService;
use Illuminate\Console\Command;

class RemoveRecordUserOtpTable extends Command
{
    protected $userOtpService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:remove-record-user-otps-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove userOtps table record when email already exists in user table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserOtpService $userOtpService) {
        $this->userOtpService = $userOtpService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('start command');

        $this->deleteMatchingRecords();

        $this->info('end command');
    }

    private function deleteMatchingRecords()
    { 

        $deletedRecords = UserOtp::query()
            ->join('users', 'user_otps.email', '=', 'users.email')
            ->delete();

    }
}
