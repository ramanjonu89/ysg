<?php
namespace App\Console;

use App\Models\UserTask;
use App\Services\UserService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('oms:sync')->hourly();
        $schedule->command('orders:restock-unpaid')->hourly();
//        $schedule->command('ysg:dailyreport')->dailyAt('00:01');

        $tasks = UserTask::all();
        $userService = app()->make(UserService::class);
        foreach ($tasks as $task) {
            $schedule->call(function () use ($task, $userService) {
                $userService->handleSchedule($task);
                Log::info($task->user_id . ' ' . \Carbon\Carbon::now());
            })->cron($task->cron_value);
//            })->everyMinute();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}