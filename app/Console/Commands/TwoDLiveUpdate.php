<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TwoDLiveUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twod:live';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        app(\App\Http\Controllers\TwoDLiveController::class)->update();
        $this->info('Finish Command For 2D Live');
    }
}
