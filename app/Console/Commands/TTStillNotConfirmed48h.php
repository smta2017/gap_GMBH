<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\NotificationController;
use App\Jobs\EmailJob;
use App\Models\RequestProductTeeTime;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class TTStillNotConfirmed48h extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:ttnotconfirm48';

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
        \Log::info('cron ttnotconfirm48');
        (new NotificationController)->ttnotconfirm48();
    }
}
