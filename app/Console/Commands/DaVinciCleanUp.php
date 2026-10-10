<?php

namespace App\Console\Commands;

use App\Helper\DaVinciHelper;
use Illuminate\Console\Command;

class DaVinciCleanUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'davinci:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily clean-up Davinci.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('davinci__CLEANUP_command');

        DaVinciHelper::cleanDaVinciPackages();
    }
}
