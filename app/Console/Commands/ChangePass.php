<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ChangePass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // using like: " php artisan user:changePass 1 abc"
    protected $signature = 'user:changePass {userId} {newPass}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $userId = $this->argument('userId');
        $newPass = $this->argument('newPass');

        $obj_user = User::find($userId);
        $obj_user->password = Hash::make($newPass);
        $obj_user->save();

        print ('ok');
    }
}
