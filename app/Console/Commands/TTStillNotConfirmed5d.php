<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Console\Command;

class TTStillNotConfirmed5d extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:ttnotconfirm5d';

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
        \Log::info('cron ttnotconfirm5d');
        (new NotificationController)->ttnotconfirm5day();
    }
}
