<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Goutte;
use Log;

use App\Models\TwoDWonNumber;
use App\Models\TwoDChangeNumber;
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
        // FirestoreController@twoDLive
        app(\App\Http\Controllers\FirestoreController::class)->twoDLive();
        $this->info('Finish Command For 2D Live');   
    }
}