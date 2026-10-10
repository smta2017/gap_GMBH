<?php

namespace App\Console\Commands;

use App\Helper\DaVinciHelper;
use Illuminate\Console\Command;

class DaVinciImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'davinci:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily import sevices.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $daVinciHelper = (new DaVinciHelper())->availablePackages();
    }
}
