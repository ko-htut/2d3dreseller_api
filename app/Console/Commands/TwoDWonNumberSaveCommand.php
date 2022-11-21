<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TwoDWonNumberSaveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twod:result';

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
     * @return int
     */
    public function handle()
    {
        app(\App\Http\Controllers\Api\TwoDController::class)->saveTwoDWonNumber();
        $this->info('Finish Command For Thai 2D Result');
    }
}
