<?php

namespace App\Jobs;

use App\Helper\DaVinciHelper;
use Exception;
use golfglobe\BewotecApi\DavinciPPSRest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;



class ImportDaVinciPackages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 4;
    public $backoff = ["60,600,3600"];


    private $criteria;
    private $packageBookingCode;
    private $durations;
    private $from;
    private $to;
    private $adults;
    private $counter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($searchCriteria)
    {
        $this->backoff = explode(',', env('BEWOTEC_DAVINCI_BACKOFF_JOB', "60,600,3600"));
        $this->criteria = $searchCriteria;
        $this->packageBookingCode = $searchCriteria["packageBookingCode"];
        $this->durations = $searchCriteria["durations"];
        $this->from = $searchCriteria["from"];
        $this->to = $searchCriteria["to"];
        $this->adults = $searchCriteria["adults"];
        $this->counter = $searchCriteria["counter"];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $davinciPPS = new DavinciPPSRest();
        if (env("APP_ENV") == "local") {
            $data = file_get_contents(env("DAVINCI_STATIC_FILE_PATH"));
        } else {
            $data = $davinciPPS->getPackagePricesAndAvailabilitiesV2($this->packageBookingCode, $this->durations, $this->from, $this->to, $this->adults);
        }

        // $uniqeID = $this->log_incoming_data($data);

        (new DaVinciHelper())->importDaVinciPackages($data);
    }

    public function failed(Exception $exception)
    {
        \Log::info($exception->getMessage());
    }
}
