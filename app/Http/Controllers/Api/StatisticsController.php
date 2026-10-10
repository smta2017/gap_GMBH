<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StatisticsMapResource;
use App\Http\Resources\StatisticsRequestResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TravelAgency;
use App\Models\Request as RequestModel;
use App\Models\Statistics;
use App\Models\RequestDestination;
use App\Models\RequestClient;
use App\Models\RequestPlayer;
use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\DMC;
use App\Models\Company;
use App\Models\GolfCourse;
use App\Models\TourOperator;
use App\Models\Product;
use App\Models\HotelProduct;
use App\Models\GolfHoliday;
use App\Models\Region;
use App\Models\Country;
use App\Models\City;

use App\Http\Resources\StatisticsResource;
use App\Models\CompanyType;
use App\Models\RequestProductTeeTime;
use App\Models\TeeTime;
use Carbon\Carbon;
use GrahamCampbell\ResultType\Success;

class StatisticsController extends Controller
{
    public function index()
    {

        $user = request()->user();

        $statistics = new Statistics();

        $statisticsData = $this->GG_statistics();

        $travel_agency = [];
        $latest_success_request_data = [];
        $successful_requests_per_month = [];
        $map_country = [];
        $most_hotels_request = [];

        $most_golfcourse_request = [];
        $most_request_hour = [];
        $most_request_day = [];


        if ($user->details->company && $user->details->company->company_type_id == "2") {
            try {
                $ag = TravelAgency::where('company_id', $user->details->company_id)->pluck('id')->toArray();

                $requestsData = RequestModel::whereIn('travel_agency_id', $ag);

                $requests = $requestsData->get();
                $users = User::count();

                $enquiries = Enquiry::where('company_id', $user->details->company_id)->count();

                $destinations_object = RequestDestination::whereIn('request_id', $requestsData->pluck('id')->toArray());
                $destinations = $destinations_object->count();
                $clients = RequestClient::whereIn('request_id', $requestsData->pluck('id')->toArray())->count();
                $players = RequestPlayer::whereIn('request_id', $requestsData->pluck('id')->toArray())->count();
                $all_products = Product::count();
                $tee_times = TeeTime::count();

                $products_count = 0;
                $requests_destinations = $destinations_object->withCount('products')->get();
                foreach ($requests_destinations as $requests_destination) {
                    $products_count += $requests_destination->products_count;
                }

                $tee_time_requests_count = 0;
                $destinations_products = $destinations_object->with(['products.requestTeeTimes' => function ($q) {
                    $q->parents();
                }])->get();
                foreach ($destinations_products as $destinations_product) {
                    $the_products = $destinations_product->products;
                    foreach ($the_products as $the_product) {
                        $tee_time_requests_count += $the_product->requestTeeTimes->count();
                    }
                }

                $gc_requests_count = 0;
                $destinations_products_GCs = $destinations_object->with(['products.requestTeeTimes' => function ($q) {
                    $q->parents();
                }])->whereHas('products', function ($q) {
                    $q->where('service_handler_type_id', 3);
                })->get();

                foreach ($destinations_products_GCs as $destinations_products_GC) {
                    $the_products = $destinations_product->products;
                    foreach ($the_products as $the_product) {
                        $gc_requests_count += $the_product->requestTeeTimes->count();
                    }                
                }


                $travel_agency = [
                    'requests' => count($requests),
                    'users' => $users,
                    'enquiries' => $enquiries,
                    'destinations' => $destinations,
                    'clients' => $clients,
                    'players' => $players,
                    'products' => $all_products,
                    'tee_times' => $tee_times,
                    'products_on_requests' => $products_count,
                    'parent_tee_times_on_requests' => $tee_time_requests_count,
                    // 'gc_requests_count' => $gc_requests_count,
                ];

                $time_excute = Carbon::now()->subYear()->toDateTimeString();
                $latest_success_request = RequestModel::with('activities')->whereHas('activities', function ($q) use ($time_excute) {
                    $q->whereJsonContains('properties->attributes->sub_status_id', 14);
                    $q->whereJsonContains('properties->attributes->status_id', 3);
                    $q->where('created_at', '>=', $time_excute);
                })->where('created_by', auth()->user()->id)->get();
                $latest_success_request_data = StatisticsRequestResource::collection($latest_success_request);

                for ($i = 0; $i < 12; $i++) {
                    for ($x = 0; $x < 2; $x++) {
                        $retVal = ($x) ? $i + 12 : $i;
                        $search_date =  Carbon::now()->subMonth($retVal);
                        $requests_month[$search_date->year . '-' . $search_date->month] = RequestModel::whereYear('created_at', $search_date->year)->whereMonth('created_at',  $search_date->month)
                            ->whereHas('activities', function ($q) {
                                $q->whereJsonContains('properties->attributes->sub_status_id', 14);
                                $q->whereJsonContains('properties->attributes->status_id', 3);
                            })->where('created_by', auth()->user()->id)->count();
                    }
                    $successful_requests_per_month[$i] = $requests_month;
                    $requests_month = [];
                }

                $map_country = StatisticsMapResource::collection(Country::withCount('hotels')->withCount('GolfCourses')->get());

                $most_golfcourse_request = RequestModel::select(\DB::raw('count(DISTINCT(requests.id)) as count'), 'request_product_tee_times.golf_course_id', 'golf_courses.name')
                    ->join('request_destinations', 'requests.id', '=', 'request_destinations.request_id')
                    ->join('request_products', 'request_destinations.id', '=', 'request_products.request_destination_id')
                    ->join('request_product_tee_times', 'request_products.id', '=', 'request_product_tee_times.request_product_id')
                    ->join('golf_courses', 'golf_courses.id', '=', 'request_product_tee_times.golf_course_id')
                    ->groupBy('request_product_tee_times.golf_course_id')
                    ->orderByDesc('count')
                    ->get();

                $most_hotels_request_a = RequestModel::select(\DB::raw('count(DISTINCT(requests.id)) as count'), 'companies.name')
                    ->join('request_destinations', 'requests.id', '=', 'request_destinations.request_id')
                    ->join('request_products', 'request_destinations.id', '=', 'request_products.request_destination_id')
                    ->join('companies', 'companies.id', '=', 'request_products.service_handler_id')
                    ->where('request_products.service_handler_type_id', 4)
                    ->whereNotNull('request_products.service_handler_id')
                    ->groupBy('request_products.service_handler_id')
                    ->orderByDesc('count');

                $most_hotels_request = RequestModel::select(\DB::raw('count(DISTINCT(requests.id)) as count'), 'companies.name')
                    ->join('request_destinations', 'requests.id', '=', 'request_destinations.request_id')
                    ->join('request_products', 'request_destinations.id', '=', 'request_products.request_destination_id')
                    ->join('hotels', 'hotels.id', '=', 'request_destinations.hotel_id')
                    ->join('companies', 'companies.id', '=', 'hotels.company_id')
                    ->where('request_products.service_handler_type_id', 4)
                    ->whereNull('request_products.service_handler_id')
                    ->groupBy('hotels.id')
                    ->groupBy('companies.name')
                    ->orderByDesc('count')
                    ->union($most_hotels_request_a)
                    ->get();

                $most_request_hour = collect(\DB::select(
                    " SELECT
                                                    DATE(created_at) as request_date,
                                                    date_format(created_at,'%H') as request_hour,
                                                    count(*) as request_count
                                                    FROM requests r 
                                                    GROUP BY DATE(created_at) , date_format(created_at,'%H')"
                ));

                $most_request_day = collect(\DB::select(
                    " SELECT
                                                    date_format(created_at,'%a') as day,
                                                    count(*) as count
                                                    FROM requests r 
                                                    GROUP BY date_format(created_at,'%a')"
                ));


                // $most_request_hour =[]; 
                // foreach ($ress as  $res) {
                //     if (!in_array($res->request_date,$most_request_hour)) {
                //         $most_request_hour[]  = $res->request_date;
                //         $most_request_hour[$res->request_date]['request_hour']  = $res->request_hour;
                //     } ; 
                // }


            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'msg' => $th->getMessage()
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'statistics' => $statisticsData,
            'travel_agency' => $travel_agency, //2 “GAP General Statistics”
            'latest_success_request' => $latest_success_request_data, //1 Last 10 Successful Requests”
            'requests_per_month' => $successful_requests_per_month, //3 “Successful Requests/Month”
            'map_country' => $map_country,
            'most_hotels_request' => $most_hotels_request, //6 ”GAP Most requested Hotels ”
            'most_golfcourse_request' => $most_golfcourse_request, // 5  ”GAP Most requested golf courses ”
            'most_request_hour' => $most_request_hour, //7 “GAP Most Requested Hour”
            'most_request_day' => $most_request_day
        ]);
    }

    public function reload_statistics()
    {

        $statistics = new Statistics();

        $statistics->set_key('hotels_number', Hotel::count());
        $statistics->set_key('dmcs_number', DMC::count());
        $statistics->set_key('travel_agencies_number', TravelAgency::count());
        $statistics->set_key('golf_clubs_number', Company::where('company_type_id', '3')->count());
        $statistics->set_key('golf_courses_number', GolfCourse::count());
        $statistics->set_key('tour_operators_number', TourOperator::count());
        $statistics->set_key('requests_number', RequestModel::count());
        $statistics->set_key('users_number', User::count());
        $statistics->set_key('enquiries_number', Enquiry::count());
        $statistics->set_key('products_number', Product::count());
        $statistics->set_key('hotel_products_number', HotelProduct::count());
        $statistics->set_key('golf_holidays_number', GolfHoliday::count());
        $statistics->set_key('clients_number', RequestClient::count());
        $statistics->set_key('players_number', RequestPlayer::count());
        $statistics->set_key('regions_number', Region::where('status', '1')->count());
        $statistics->set_key('countries_number', Country::where('status', '1')->whereHas('cities')->count());
        $statistics->set_key('cities_number', City::where('status', '1')->count());

        (new RequestController())->close_requests();
        return true;
    }

    public function GG_statistics()
    {
        $statistics =  [
            'hotels_count' => Hotel::count(),
            'dmcs_count' => DMC::count(),
            'travel_agencies_count' => TravelAgency::count(),
            'golf_clubs_count' => Company::where('company_type_id', '3')->count(),
            'golf_courses_count' => GolfCourse::count(),
            'tour_operators_count' => TourOperator::count(),
            'requests_count' => RequestModel::count(),
            'users_count' => User::count(),
            'enquiries_count' => Enquiry::count(),
            'products_count' => Product::count(),
            'hotel_products_count' => HotelProduct::count(),
            'golf_holidays_count' => GolfHoliday::count(),
            'clients_count' => RequestClient::count(),
            'players_count' => RequestPlayer::count(),
            'regions_count' => Region::where('status', '1')->count(),
            'countries_count' => Country::where('status', '1')->whereHas('cities')->count(),
            'cities_count' => City::where('status', '1')->count(),
            'request_product_tee_time_count' => RequestProductTeeTime::count(),
            'gc_requests_count' => RequestProductTeeTime::distinct('golf_course_id')->count()
        ];
        return $statistics;
    }
}
