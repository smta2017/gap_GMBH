<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Console\Command;

class RequestNotSubmited10d extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:requestnotsubmited10d';

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
        \Log::info('cron requestnotsubmited10d');
        (new NotificationController)->requestnotsubmited10d();
    }
}
