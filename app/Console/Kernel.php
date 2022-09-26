<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    
    protected $commands = [
        Commands\WebSocketCommand::class
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('websocket:init')->timezone('Asia/Yangon')->cron('* * * * 1-5');
        $schedule->call('\App\Http\Controllers\TwoDWonNumberController@store')->timezone('Asia/Yangon')->cron('01 12 * * 1-5');
        $schedule->call('\App\Http\Controllers\TwoDWonNumberController@store')->timezone('Asia/Yangon')->cron('30 16 * * 1-5');
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
