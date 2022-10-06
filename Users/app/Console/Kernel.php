<?php

namespace App\Console;

use App\Jobs\AdminDigest;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\User;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --stop-when-empty')->everyMinute();

        $schedule->call(function () {
            $count = User::with('getNotApproved')->count();
            $admins = User::where('role_id', '=', 3)->get();
            foreach ($admins as $admin) {
                $data = [
                    'email' => $admin['email'],
                    'name' => $admin['name'],
                ];
                // $e = $admin->email;
                dispatch(new AdminDigest($data, $count));
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
