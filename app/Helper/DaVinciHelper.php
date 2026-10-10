<?php

namespace App\Helper;

use App\Jobs\CleanUpDaVinciPackages;
use App\Jobs\ImportDaVinciPackages;
use App\Models\BewotecDavinciService;
use App\Models\BewotecDavinciServiceType;
use Carbon\Carbon;
use Exception;
use golfglobe\BewotecApi\DavinciPPSRest;
use golfglobe\BewotecApi\DavinciUtils;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Queue;

class DaVinciHelper
{
    public function __construct()
    {
    }

    public static function cleanDaVinciPackages()
    {
        $hoursAgo = now()->subHours(\env('BEWOTEC_DAVINCI_CLEAN_UP_SUB_HOURS', 24));
        BewotecDavinciService::where('sync_last', '<=', $hoursAgo)->delete();
        BewotecDavinciServiceType::where('sync_last', '<=', $hoursAgo)->delete();

        // BewotecDavinciServiceType::where('price', '<=', 0)->delete();

        for ($i = 0; $i < 2; $i++) {
            $SQL = "select id FROM bewotec_davinci_services WHERE 
                        (
                            (requirement IN ('H', 'SO', 'T') AND NOT EXISTS (
                                SELECT 1 FROM bewotec_davinci_service_types WHERE bewotec_davinci_services.id = bewotec_davinci_service_types.service_id
                            ))
                            OR
                            (requirement = 'P' AND id NOT IN (
                                SELECT package_service_id FROM bewotec_davinci_services AS children WHERE bewotec_davinci_services.id = children.package_service_id
                            ))
                        );
                ";

            $ids = collect(\DB::select($SQL))->pluck('id')->toArray();
            $chunkSize = 500; // Adjust the batch size

            $chunks = array_chunk($ids, $chunkSize);

            foreach ($chunks as $chunk) {
                $placeholders = implode(',', array_fill(0, count($chunk), '?'));

                $sql = "DELETE FROM bewotec_davinci_services WHERE id IN ($placeholders)";

                \DB::delete($sql, $chunk);
            }
        }
    }


    public function availablePackages($request = null)
    {
        $codes = $this->getBookingCodes($request);

        $mainCounter = 0;
        // $codes=['GMZ1110IP'];
        foreach ($codes  as $code) {
            $mainCounter += $this->getDavinciPackage($code);
        }
        CleanUpDaVinciPackages::dispatch()->onQueue('davinci_auto_import');
        return $mainCounter;
    }

    public function getDavinciSpecificPackage($request = null)
    {
        $x = 1;
        $criteria = [
            "counter" => 0,
            "packageBookingCode" => $request["bookingCode"],
            "durations" => $request["duration_request"],
            "from" => $request["fromDate"],
            "to" => $request["toDate"],
            "adults" => $request["adult"],
        ];

        $davinciPPS = new DavinciPPSRest();

        $data = $davinciPPS->getPackagePricesAndAvailabilitiesV2($criteria["packageBookingCode"], $criteria["durations"], $criteria["from"], $criteria["to"], $criteria["adults"]);

        (new DaVinciHelper())->importDaVinciPackages($data);

        // ImportDaVinciPackages::dispatch($criteria)->onQueue('davinci_auto_import');
        return $data;
    }

    public function getDavinciPackage($bookingCode)
    {
        $counter = 0;
        $duration_requests = [];
        $duration_request_tmp = [];
        for ($i = 1; $i <= 28; ++$i) {
            $duration_request_tmp[] = $i;
            if ($i % 2 === 0) {
                $duration_requests[] = $duration_request_tmp;
                $duration_request_tmp = [];
            }
        }

        foreach ([1, 2, 3, 4] as $adult) {
            $fromDate = new \DateTime();
            $fromDateMax = (new \DateTime())->modify('+1 year');

            do {
                $toDate = clone $fromDate;
                $toDate->modify('+3 month');

                foreach ($duration_requests as $duration_request) {

                    $toDateDurationRel = clone $toDate;

                    $criteria = [
                        "counter" => $counter,
                        "packageBookingCode" => $bookingCode,
                        "durations" => $duration_request,
                        "from" => $fromDate->format('Y-m-d'),
                        "to" => $toDateDurationRel->modify('+' . max($duration_request) . ' days')->format('Y-m-d'),
                        "adults" => $adult,
                    ];

                    // ===============  SEND TO QUEUE  ===============
                    $JOBS_LIMITER = (int) env("BEWOTEC_JOBS_LIMITER");

                    $queueSize = Queue::size('davinci_auto_import');

                    if ($JOBS_LIMITER == 0) {
                        $counter++;
                        $criteria2 = [
                            "counter" => $counter,
                            "packageBookingCode" => $bookingCode,
                            "durations" => "[" . \implode(',', $duration_request) . "]",
                            "from" => $fromDate->format('Y-m-d'),
                            "to" => $toDateDurationRel->modify('+' . max($duration_request) . ' days')->format('Y-m-d'),
                            "adults" => $adult,
                        ];
                        \Log::info(\implode(",", $criteria2));
                        ImportDaVinciPackages::dispatch($criteria)->onQueue('davinci_auto_import');
                    } else {
                        if ($queueSize <= $JOBS_LIMITER) {
                            $counter++;


                            $criteria2 = [
                                "counter" => $counter,
                                "packageBookingCode" => $bookingCode,
                                "durations" => "[" . \implode(',', $duration_request) . "]",
                                "from" => $fromDate->format('Y-m-d'),
                                "to" => $toDateDurationRel->modify('+' . max($duration_request) . ' days')->format('Y-m-d'),
                                "adults" => $adult,
                            ];
                            \Log::info(\implode(",", $criteria2));
                            ImportDaVinciPackages::dispatch($criteria)->onQueue('davinci_auto_import');
                        } else {
                            break;
                            return;
                        }
                    }
                    // ===============  SEND TO QUEUE  ===============


                }

                $fromDate->modify('+3 month');
            } while ($fromDate <= $fromDateMax);
        }
        return $counter;
    }


    public function importDaVinciPackages($data = null, $loger_id = null)
    {

        $jsonData = json_decode($data, true);

        $time_periods = $jsonData["Packages"][0]["Schedules"];

        \DB::statement("ALTER TABLE bewotec_davinci_services AUTO_INCREMENT =  1");
        \DB::statement("ALTER TABLE bewotec_davinci_service_types AUTO_INCREMENT =  1");

        foreach ($time_periods as $time_period) {

            if (isset($time_period["OccupancyBoards"]) && count($time_period["OccupancyBoards"]) > 0) {

                $services = $time_period["OccupancyBoards"][0]["ServiceGroups"][0]["Services"];

                $parent_service_id = null;
                $new_service = null;

                $is_valid_service = self::isValidService($services);

                if ($is_valid_service) {
                    foreach ($services as $service) {
                        if ($service["Requirement"] == "P") {
                            $new_service = self::createParentService($service);
                            $parent_service_id = $new_service->id;
                        } else if ($service["Requirement"] == "H" && ($service['From'] == $time_period["From"] && $service['To'] == $time_period["To"])) {
                            $new_service = self::createService($service, $parent_service_id);
                        } else if (in_array($service["Requirement"], ['SO', 'T'])) {
                            $new_service = self::createService($service, $parent_service_id);
                        }

                        // log service with data incoming
                        // $filename = DIRECTORY_SEPARATOR . 'pivot_log' . '.log';
                        // $filePath = public_path($filename);
                        // \File::append($filePath,  $new_service->id . ' -> ' . $loger_id  . "\n" . str_repeat("=", 50) . "\n\n");
                    }
                }
            }
        }
    }

    private static function isValidService($services)
    {
        $returned_val = false;

        $values = array_column($services, 'Requirement');
        $desiredValues = ["P"];

        $difference = array_diff($desiredValues, $values);
        if (empty($difference)) {
            $returned_val =  true;
        }

        $searchValues = ["H", "T", "SO"];
        $commonValues = array_intersect($values, $searchValues);

        if (!empty($commonValues)) {
            // At least one value from $searchValues exists in $values
            $returned_val = true;
        } else {
            $returned_val = false;
        }

        return $returned_val;
    }

    private static function createParentService($service)
    {
        $service_converted_date_from = self::convertDate($service["From"]);
        $service_converted_date_to = self::convertDate($service["To"]);

        $duration = self::durationCalc($service_converted_date_from, $service_converted_date_to);

        $data = [
            "requirement"  => $service["Requirement"],              //: "P",
            "booking_code" => $service["BookingCode"],              //: "GMZ1110P",
            "date_from" => self::convertDate($service["From"]),     //: "/Date(1687298400000+0200)/",
            "date_to"  =>  self::convertDate($service["To"]),       //: "/Date(1687903200000+0200)/",
            "duration" =>   $duration,
            "booking_code_id"  => $service["BookingCodeId"],        //: 7809,
            "booking_code_name" => $service["BookingCodeName"],     //: "Jardin Tecina Paket",
            "catalog_code" => $service["CatalogueCode"],            //: "2223",
            "catalog_name" => $service["CatalogueName"],            //: "GOLF GLOBE ab 2022",
            "package_order" => $service["OrderInPackage"],          //: 0
        ];


        //create parent sevice
        $new_service = BewotecDavinciService::updateOrCreate($data);

        if (!$new_service->wasRecentlyCreated) {
            // Existing record was updated
            $new_service->update(["sync_last" => date("Y-m-d H:i:s")]);

            //Delete all related services
            // BewotecDavinciService::where('package_service_id', $new_service->id)->delete();
        }


        return $new_service;
    }

    private static function createService($service, $parent_service_id)
    {
        // serviceTypes not empty && TypeOfAssignment is 4
        try {
            if (self::serviceHasTypes($service) && in_array($service["TypeOfAssignment"], [1, 4])) {

                $service_converted_date_from = self::convertDate($service["From"]);
                $service_converted_date_to = self::convertDate($service["To"]);

                $duration = self::durationCalc($service_converted_date_from, $service_converted_date_to);
                $data = [
                    "requirement" =>                     $service["Requirement"], //  "H",
                    "booking_code" =>                    $service["BookingCode"], //  "GMZ11105",
                    "date_from" =>                       $service_converted_date_from, //  "\/Date(1687298400000+0200)\/",
                    "date_to" =>                         $service_converted_date_to, //  "\/Date(1687730400000+0200)\/",
                    "booking_code_id" =>                 $service["BookingCodeId"], //  7807,
                    "booking_code_name" =>               $service["BookingCodeName"], //  "Jardin Tecina Golf Package (5N+3GF)",
                    "catalog_code" =>                    $service["CatalogueCode"], //  "2223",
                    "catalog_name" =>                    $service["CatalogueName"], //  "GOLF GLOBE ab 2022",
                    "package_type_of_assignment" =>      $service["TypeOfAssignment"], //  3,
                    "package_order" =>                   $service["OrderInPackage"], //  2,
                    "duration" =>                        $duration,
                    "standard_meal_code" => ($service["Requirement"] == "H") ? $service["StandardMealCode"] : null, //  "H",
                    "destination_name" => (isset($service["DestinationName"])) ? $service["DestinationName"] : null, //  "La Gomera",
                    "destination_code" => (isset($service["DestinationCode"])) ? $service["DestinationCode"] : null, //  "GMZ"
                    "package_service_id" =>              $parent_service_id
                ];

                $conditions = [
                    "booking_code" => $service["BookingCode"],
                    "requirement" => $service["Requirement"],
                    "catalog_code" => $service["CatalogueCode"],
                    "date_from" => $service_converted_date_from,
                    "date_to" => $service_converted_date_to,
                    "duration" => $duration,
                    "package_order" => $service["OrderInPackage"],
                    "package_service_id" => $parent_service_id,
                ];

                // check doublicated service
                $new_service = BewotecDavinciService::where($conditions)->first();


                if ($new_service) {
                    $new_service->update(array_merge($data, ["sync_last" => date("Y-m-d H:i:s")]));
                } else {
                    //create child sevice
                    $new_service = BewotecDavinciService::create($data);
                }

                self::storeServiceTypes($service, $new_service->id);

                return $new_service;
            }
        } catch (\Throwable $th) {
            \Log::info($service);
        }
    }

    private static function storeServiceTypes($service, $service_id)
    {
        $service_types =  self::getServicesTypes($service);

        foreach ($service_types as $service_type) {

            if (self::serviceTypeValid($service_type)) {

                $data = [
                    "service_id" => $service_id,
                    "service_type_code" => (isset($service_type["Code"])) ? $service_type["Code"] : $service_type["OccupancyBoard"],
                    "service_type_name" => (isset($service_type["Code_Name"])) ? $service_type["Code_Name"] : $service_type["OccupancyBoardName"],
                    "participants" =>   $service_type["Participants"],
                    "adults" => $service_type["Adults"],
                    "price" =>  $service_type["Price"],
                    "price_avg" =>  $service_type["Price"] / $service_type["Participants"],
                    "price_booking_related" =>  $service_type["PriceIsBookingRelated"],
                    "currency" =>   $service_type["Currency"],
                    "availability" =>   $service_type["Availability"],
                    "availability_detailed" =>  $service_type["DetailedAvailability"],
                    "occupation_minimum" => $service_type["MinimumOccupation"],
                    "occupation_maximum" => $service_type["MaximumOccupation"],
                    "adults_minimum" => (isset($service_type["MinimumAdults"])) ? $service_type["MinimumAdults"] : \null,
                    "adults_maximum" => (isset($service_type["MaximumAdults"])) ? $service_type["MaximumAdults"] : \null,
                    "childs_maximum" => (isset($service_type["MaximumChilds"])) ? $service_type["MaximumChilds"] : \null,
                    "babys_maximum" => (isset($service_type["MaximumBabys"])) ? $service_type["MaximumBabys"] : \null,
                ];


                $conditions = [
                    'service_id' =>  $service_id,
                    'service_type_code' => (isset($service_type["Code"])) ? $service_type["Code"] : $service_type["OccupancyBoard"],
                    'participants' => $service_type["Participants"],
                    'adults' => $service_type["Adults"],
                ];

                // check doublicated service type
                $new_service_type = BewotecDavinciServiceType::where($conditions)->first();

                if ($new_service_type) {
                    $new_service_type->update(array_merge($data, ["sync_last" => date("Y-m-d H:i:s")]));
                } else {
                    //create new sevice type
                    $new_service_type = BewotecDavinciServiceType::create($data);
                }
            }
        }
    }

    public function deletePackage($request = null)
    {
        
            if (!isset($request->booking_code) && $request->booking_code == '') {
                throw new Exception('No Booking Code provided.');
            }

            $ids = BewotecDavinciService::whereBookingCode($request->booking_code)->pluck('id')->toArray();

            $chunkSize = 500; // Adjust the batch size

            $chunks = array_chunk($ids, $chunkSize);

            foreach ($chunks as $chunk) {
                $placeholders = implode(',', array_fill(0, count($chunk), '?'));

                $sql = "DELETE FROM bewotec_davinci_service_types WHERE service_id IN ($placeholders)";
                \DB::delete($sql, $chunk);

                $sql = "DELETE FROM bewotec_davinci_services WHERE id IN ($placeholders)";
                \DB::delete($sql, $chunk);
            }

            $this->cleanDaVinciPackages();
      
    }

    private static function serviceHasTypes($service)
    {
        if ((isset($service["Rooms"]) && count($service["Rooms"])) || (isset($service["ServiceTypes"]) && count($service["ServiceTypes"]))) {
            return \true;
        }
        return \false;
    }

    private static function getServicesTypes($service)
    {
        $service_types = [];
        if ((isset($service["Rooms"]) && count($service["Rooms"]))) {
            $service_types = $service["Rooms"];
        } else if ((isset($service["ServiceTypes"]) && count($service["ServiceTypes"]))) {
            $service_types = $service["ServiceTypes"];
        }
        return $service_types;
    }

    private static function convertDate($date = null)
    {
        $dt = DavinciUtils::dateTimeFromMSJson($date);
        return $dt->format('Y-m-d');
    }

    private static function serviceTypeValid($service_type)
    {
        if (isset($service_type["PriceErrorInfo"]) && !is_null($service_type["PriceErrorInfo"])) {
            return \false;
        }

        if (!isset($service_type["Price"]) /*|| $service_type["Price"] <= 0*/) {
            return \false;
        }

        return \true;
    }




    private static function durationCalc($date_from, $date_to)
    {
        $date1 = Carbon::parse($date_from);
        $date2 = Carbon::parse($date_to);

        $diffInDays = $date1->diffInDays($date2);

        return $diffInDays;
    }


    public function getBookingCodes($request = null)
    {
        $davinciPPS = new \golfglobe\BewotecApi\DavinciPPSRest();

        $nowDate = Carbon::now();

        $octoberFirstLastYear = $nowDate->subYear()->startOfYear()->addMonths(9)->toDateString();
        $septemberThirtiethNextYear = $nowDate->addYear()->startOfYear()->addMonths(8)->endOfMonth()->toDateString();

        $startDate = $octoberFirstLastYear; //"2022-10-01"
        $endDate = $septemberThirtiethNextYear; //"2024-09-30"

        if (env("APP_ENV") == "local") {
            $codes = ['GMZ1110P', 'VAR31331'];
        } elseif (isset($request->booking_code) && $request->booking_code != "") {
            $codes = [$request->booking_code];
        } else {
            $data = $davinciPPS->GetOverviewOfValidPackages($startDate, $endDate);
            $jsonData = json_decode($data, true);
            $codes =   array_column($jsonData, 'Code');
        }
        return $codes;
    }
}
