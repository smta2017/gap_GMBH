<?php

namespace App\Http\Controllers\Api;

use App\Helper\NotificationsHelper;
use App\Helper\TeeTimeNotificationsHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\RequestClient;
use App\Models\RequestStatus;
use App\Models\RequestProductStatus;
use App\Models\RequestTeeTimeStatus;
use App\Models\RequestDestination;
use App\Models\RequestProduct;
use App\Models\RequestProductDetails;
use App\Models\RequestProductTeeTime;
use App\Models\RequestPlayer;
use App\Models\RequestType;
use App\Models\LeaderType;
use App\Models\Product;
use App\Models\RequestRedirect;
use App\Models\RequestRedirectEmail;
use App\Models\ProductDetails;
use App\Models\RequestDocument;
use App\Models\TravelAgency;
use App\Models\TourOperator;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Hotel;
use App\Models\GolfCourse;
use App\Models\DMC;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\EmailLog;
use App\Models\EmailLogEmail;
use App\Http\Resources\RequestResource;
use App\Http\Resources\RequestDetailsResource;
use App\Http\Resources\RequestCommentResource;
use App\Http\Resources\RequestStatusResource;
use App\Http\Resources\RequestDestinationResource;
use App\Http\Resources\RequestClientResource;
use App\Http\Resources\RequestPlayerResource;
use App\Http\Resources\RequestProductResource;
use App\Http\Resources\RequestTeeTimeResource;
use App\Http\Resources\RequestAlternativeTeeTimeResource;
use App\Http\Resources\RequestDocumentResource;
use App\Http\Resources\BaseDataResource;
use App\Http\Resources\RequestLogsResource;
use App\Http\Resources\RequestStatusLogsResource;
use App\Http\Resources\RequestProductStatusLogsResource;
use App\Http\Resources\RequestTeeTimeStatusLogsResource;
use App\Http\Resources\HandlerResource;
use App\Http\Resources\HandlerDataResource;
use App\Http\Resources\HandlerGCResource;
use App\Http\Resources\RequestAlternativeTeeTimeViewResource;
use App\Http\Resources\RequestDetailsTeeTimeViewResource;
use App\Http\Resources\RequestTeeTimeViewResource;
use App\Http\Resources\TeeTimeDatesResource;
use App\Http\Resources\UserFullDataResource;
use Spatie\Activitylog\Models\Activity;
use App\Mail\RequestClientMail;
use App\Models\DmcCity;
use App\Models\Permission;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;
use Mail;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Gcd;

class RequestController extends Controller
{
    public function index()
    {
        $requestInst = new RequestModel();


        $requests = $requestInst->get_pagination();

        foreach ($requests as $key => $request) {
            foreach ($request->destinations as $destination) {
                $prdoducts = $destination->products;
                foreach ($destination->products as $key => $product) {
                    if (request()->handler_name) {
                        if ($product->get_service_handler_info()) {
                            if (!str_contains($product->get_service_handler_info()->name, request()->handler_name)) {
                                unset($destination->products[$key]);
                            }
                        }
                    }
                }
            }
        }
        $requestsData = RequestDetailsResource::collection($requests);

        return response()->json([
            'status' => true,
            'requests' => $requestsData->response()->getData()
        ]);
    }

    public function show($id)
    {
        $request = RequestModel::findOrFail($id);

        $requestData = new RequestDetailsResource($request);

        return response()->json([
            'status' => true,
            'request' => $requestData,
        ]);
    }

    public function get_types()
    {
        $types = RequestType::select(['id', 'name', 'status'])->get();

        return response()->json([
            'status' => true,
            'types' => $types
        ]);
    }

    public function get_statuses()
    {
        $statuses = new RequestStatus();

        $statusedData = RequestStatusResource::collection($statuses->get());

        return response()->json([
            'status' => true,
            'statuses' => $statusedData
        ]);
    }

    public function get_requests()
    {
        $requests = RequestModel::whereHas('destinations', function ($query) {
            $query->whereHas('products', function ($q) {
                $q->whereHas('requestTeeTimes');
            });
        })
            ->paginate(10);

        $requestsData = RequestDetailsTeeTimeViewResource::collection($requests);
        return response()->json([
            'status' => true,
            'requests' => $requestsData->response()->getData()
        ]);
    }

    public function get_company_tee_times($id, Request $request)
    {


        $company = Company::find($id);
        $request_tee_times = RequestTeeTimeViewResource::collection($company->teeTimes());


        return response()->json([
            'status' => true,
            'tee_times' => $request_tee_times
        ]);
    }


    public function get_requests_tee_times($id, Request $request)
    {
        $requests = RequestModel::find($id);

        $request_tee_times = RequestTeeTimeViewResource::collection($requests->get_teeTimes());

        return response()->json([
            'status' => true,
            'tee_times' => $request_tee_times
        ]);
    }


    public function get_golfCourse_tee_times($id, Request $request)
    {
        $golfcours = GolfCourse::find($id);

        $golfcour_tee_times = RequestTeeTimeViewResource::collection($golfcours->get_teeTimes());

        return response()->json([
            'status' => true,
            'tee_times' => $golfcour_tee_times
        ]);
    }

    public function get_travelAgency_tee_times($id, Request $request)
    {
        $travel_agency = TravelAgency::find($id);
        $request_tee_times = RequestTeeTimeViewResource::collection($travel_agency->get_teeTimes());

        return response()->json([
            'status' => true,
            'tee_times' => $request_tee_times
        ]);
    }

    public function get_tourOperator_tee_times($id, Request $request)
    {
        $tour_operator = TourOperator::find($id);
        $request_tee_times = RequestTeeTimeViewResource::collection($tour_operator->get_teeTimes());

        return response()->json([
            'status' => true,
            'tee_times' => $request_tee_times
        ]);
    }

    public function get_tee_times_by_date(Request $request)
    {
        $validated = $request->validate([
            'tee_time_date' => 'required',
        ]);

        $date = $request['tee_time_date'];

        $requestProductTeeTime = RequestProductTeeTime::whereNull('parent_id')
            ->whereDate('date', $date)
            ->orWhereHas('alternatives', function ($q) use ($date) {
                $q->whereDate('date', $date);
            })
            ->orderBy('date', 'DESC')
            ->paginate(10);

        $request_tee_times = RequestTeeTimeViewResource::collection($requestProductTeeTime);
        return response()->json([
            'status' => true,
            'tee_times' => $request_tee_times
        ]);
    }

    public function get_tee_times_by_request_date(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required',
        ]);

        $requestProductTeeTime = RequestProductTeeTime::where('parent_id', null)->whereHas('requestProduct.destination.request', function ($q) use ($request) {
            $q->whereDate('created_at', $request['request_date']);
        })->get();

        $request_tee_times = RequestTeeTimeViewResource::collection($requestProductTeeTime);

        return response()->json([
            'status' => true,
            'tee_times' => $request_tee_times
        ]);
    }

    public function get_handlers()
    {
        $search = request()->search;
        $company_id = request()->company_id;


        $companies = Company::whereHas('requestProductHandler')
            // ->whereHas('teeTimes')
            ->where('name', 'LIKE', '%' . $search . '%');

        if ($company_id) {
            $companies = $companies->where('id', $company_id);
        }

        $companies = $companies->get();


        $productRequestEmptyHandlers = RequestProduct::where('service_handler_id', null)->get();

        $companyIDs = [];
        foreach ($productRequestEmptyHandlers as $h) {
            if ($h->get_service_handler_info()) {
                $companyIDs[] = $h->get_service_handler_info()->id;
            }
        }

        $handlerCompanies = Company::whereIn('id', $companyIDs);
        if ($company_id) {
            $handlerCompanies = $handlerCompanies->where('id', $company_id);
        }

        $handlerCompanies = $handlerCompanies->get();

        $allCompanyItems = new \Illuminate\Database\Eloquent\Collection;

        $allCompanyItems = $allCompanyItems->concat($companies);
        $allCompanyItems = $allCompanyItems->concat($handlerCompanies);
        $allCompanyItems = $this->paginate($allCompanyItems);

        $allItemsData = HandlerResource::collection($allCompanyItems);

        return response()->json([
            'status' => true,
            'handlers' => $allItemsData->response()->getData()
        ]);
    }

    public function paginate($items, $perPage = 3, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function get_golfcourses()
    {
        $search = request()->search;
        $company_id = request()->company_id;

        $courses = GolfCourse::whereHas('handledTeeTimes')
            // ->whereHas('teeTimes')
            ->where('active', '1')
            ->where('name', 'LIKE', '%' . $search . '%')
            ->select(['id', 'name', 'company_id']);

        if ($company_id) {
            $courses = $courses->where('company_id', $company_id);
        }
        $courses = $courses->paginate(10);

        $coursesData = HandlerGCResource::collection($courses);
        return response()->json([
            'status' => true,
            'golf_courses' => $coursesData->response()->getData()
        ]);
    }

    public function get_agencies_tee_times()
    {
        $search = request()->search;
        $company_id = request()->company_id;

        $agencies = TravelAgency::whereHas('requests', function ($query) {
            $query->whereHas('destinations', function ($sub) {
                $sub->whereHas('products', function ($q) {
                    $q->whereHas('requestTeeTimes');
                });
            });
        })
            ->select(['id', 'name', 'company_id']);

        if ($company_id) {
            $agencies = $agencies->where('company_id', $company_id);
        }

        $agencies = $agencies->paginate(10);

        $agenciesData = HandlerDataResource::collection($agencies);
        return response()->json([
            'status' => true,
            'agencies' => $agenciesData->response()->getData()
        ]);
    }

    public function get_operators_tee_times()
    {
        $search = request()->search;
        $company_id = request()->company_id;

        $operators = TourOperator::whereHas('requests', function ($query) {
            $query->whereHas('destinations', function ($sub) {
                $sub->whereHas('products', function ($q) {
                    $q->whereHas('requestTeeTimes');
                });
            });
        })
            ->select(['id', 'name', 'company_id']);

        if ($company_id) {
            $operators = $operators->where('company_id', $company_id);
        }

        $operators = $operators->paginate(10);
        $operatorsData = HandlerDataResource::collection($operators);

        return response()->json([
            'status' => true,
            'operators' => $operatorsData->response()->getData()
        ]);
    }

    public function get_date_tee_times()
    {
        $dates = RequestProductTeeTime::selectRaw('DATE(date) as the_date, count(*) as views')
            ->groupBy('the_date')
            ->orderBy('the_date', 'DESC')
            ->paginate(10);

        $datesData = TeeTimeDatesResource::collection($dates);

        return response()->json([
            'status' => true,
            'dates' => $datesData->response()->getData()
        ]);
    }

    public function get_request_date_tee_times()
    {
        $dates = RequestModel::whereHas('destinations', function ($query) {
            $query->whereHas('products', function ($q) {
                $q->whereHas('requestTeeTimes');
            });
        })->select(DB::raw('DATE(created_at) as the_date'), DB::raw('count(*) as views'))
            ->groupBy('the_date')
            ->orderBy('the_date', 'DESC')
            ->paginate(10);

        $datesData = TeeTimeDatesResource::collection($dates);

        return response()->json([
            'status' => true,
            'dates' => $datesData->response()->getData()
        ]);
    }

    public function get_agencies_operators()
    {
        $search = request()->search;
        $company_id = request()->company_id;

        $agencies = TravelAgency::join('companies', 'companies.id', 'travel_agencies.company_id')
            ->join('company_types', 'company_types.id', 'companies.company_type_id')
            ->where('travel_agencies.active', '1')
            ->where('travel_agencies.name', 'LIKE', '%' . $search . '%')
            ->select(
                [
                    'travel_agencies.id',
                    'travel_agencies.name',
                    'company_types.id AS company_type_id',
                    'company_types.name AS company_type_name'
                ]
            );

        if ($company_id) {
            $agencies = $agencies->where('company_id', $company_id);
        }

        $agencies = $agencies->get();

        $operatoes = TourOperator::join('companies', 'companies.id', 'tour_operators.company_id')
            ->join('company_types', 'company_types.id', 'companies.company_type_id')
            ->where('tour_operators.active', '1')
            ->where('tour_operators.name', 'LIKE', '%' . $search . '%')
            ->select(
                [
                    'tour_operators.id',
                    'tour_operators.name',
                    'company_types.id AS company_type_id',
                    'company_types.name AS company_type_name'
                ]
            );

        if ($company_id) {
            $operatoes = $operatoes->where('company_id', $company_id);
        }

        $operatoes = $operatoes->get();

        $combined = $agencies->merge($operatoes);

        return response()->json([
            'status' => true,
            'agencies_operators' => $combined
        ]);
    }

    public function check_request()
    {
        $tuiRefCode = request()->tui_ref_code;

        $requestModel = null;

        if ($tuiRefCode) {
            $requestData = RequestModel::where('tui_ref_code', $tuiRefCode)->first();

            if ($requestData) {
                $requestModel = new RequestDetailsResource($requestData);
            }
        }

        return response()->json([
            'status' => true,
            'request' => $requestModel
        ]);
    }

    public function get_product_statuses()
    {
        $statuses = new RequestProductStatus();

        $statusedData = BaseDataResource::collection($statuses->where('status', '1')->get());

        return response()->json([
            'status' => true,
            'statuses' => $statusedData
        ]);
    }

    public function get_tee_time_statuses()
    {
        $statuses = new RequestTeeTimeStatus();

        $statusedData = BaseDataResource::collection($statuses->where('status', '1')->get());

        return response()->json([
            'status' => true,
            'statuses' => $statusedData
        ]);
    }

    public function get_leader_types()
    {
        $types = LeaderType::select(['id', 'name', 'has_hcp', 'has_company'])->get();

        return response()->json([
            'status' => true,
            'types' => $types
        ]);
    }

    public function get_vouchers($id)
    {
        $request = RequestModel::findOrFail($id);

        $destinations = RequestDestination::where('request_id', $id)->pluck('id')->toArray();
        $p = RequestProduct::where('request_destination_id', $destinations)->pluck('id')->toArray();

        $teeTimes = RequestProductTeeTime::whereIn('request_product_id', $p)->where('parent_id', null);

        if (request()->request_product_id) {
            $teeTimes = $teeTimes->where('request_product_id', request()->request_product_id);
        }

        $teeTimesData = RequestTeeTimeResource::collection($teeTimes->get());

        $requestData = new RequestDetailsResource($request);

        return response()->json([
            'status' => true,
            'request_tee_times' => $teeTimesData,
            'request' => $requestData
        ]);
    }

    public function get_delegate_client_token($id)
    {
        $request = RequestModel::findOrFail($id);

        $user = User::where('player_id', $request->delegate_player_id)->first();

        $token = null;
        if ($user) {
            $tokenData = \DB::table('password_resets')->where('email', $user->id)->first();

            if ($tokenData) {
                $token = $tokenData->token;
            }
        }

        return response()->json([
            'status' => true,
            'token' => $token
        ]);
    }

    public function get_voucher($code)
    {
        $teeTime = RequestProductTeeTime::where('voucher_code', $code)->first();

        if (!$teeTime) {
            return response()->json([
                'status' => false,
            ]);
        }

        $requestModel = $teeTime->requestProduct->destination->request;

        $requestData = new RequestDetailsResource($requestModel);
        $teeTimeData = new RequestTeeTimeResource($teeTime);

        return response()->json([
            'status' => true,
            'request_tee_time' => $teeTimeData,
            'request' => $requestData
        ]);
    }

    public function get_logs($id)
    {
        $request = RequestModel::find($id);

        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $logs = Activity::where(function ($query) use ($id) {
            $query->where('subject_type', 'App\Models\Request')
                ->where('subject_id', $id);
        })
            ->orWhere(function ($query) use ($id) {
                $query->where('subject_type', 'App\Models\RequestDestination')
                    ->where('properties->request_id', $id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('subject_type', 'App\Models\RequestClient')
                    ->where('properties->request_id', $id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('subject_type', 'App\Models\RequestPlayer')
                    ->where('properties->request_id', $id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('subject_type', 'App\Models\RequestProduct')
                    ->where('properties->request_id', $id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('subject_type', 'App\Models\RequestProductDetails')
                    ->where('properties->request_id', $id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('subject_type', 'App\Models\RequestProductTeeTime')
                    ->where('properties->request_id', $id);
            })
            ->orderBy('id', 'DESC')->paginate($pagination);


        $logsData = RequestLogsResource::collection($logs);

        return response()->json([
            'status' => true,
            'logs' => $logsData->response()->getData()
        ]);
    }

    public function get_status_logs($id)
    {
        $request = RequestModel::find($id);

        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $logs = Activity::where('subject_type', 'App\Models\Request')
            ->where('subject_id', $id)
            ->where(function ($q) {
                $q->where('description', 'created')->orWhere('description', 'updated');
            })
            ->where(function ($q) {
                $q->where('properties', 'LIKE', '%status.name%')
                    ->orWhere('properties', 'LIKE', '%subStatus.name%');
            })
            ->orderBy('id', 'ASC')->get();


        $logsData = RequestStatusLogsResource::collection($logs);

        return response()->json([
            'status' => true,
            'logs' => $logsData
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            // 'travel_agency_id' => 'required|exists:travel_agencies,id',
            // 'phone' => 'required',
            // 'fax' => 'required',
            // 'email' => 'required|email',

            'type_id' => 'required|exists:request_types,id',

            'booking_codes' => 'array'

        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;
            $data['status_id'] = 1;
            $data['sub_status_id'] = 1;

            $requestModel = RequestModel::create($data);

            if (is_array($request->booking_codes) && count($request->booking_codes) > 0) {
                foreach ($request->booking_codes as $code) {
                    $requestModel->codes()->create(['booking_code' => $code]);
                }
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_bulk(Request $request)
    {
        $validated = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            // 'travel_agency_id' => 'required|exists:travel_agencies,id',
            // 'phone' => 'required',
            // 'fax' => 'required',
            // 'email' => 'required|email',

            'type_id' => 'required|exists:request_types,id',

            'booking_codes' => 'array'

        ]);

        try {
            DB::beginTransaction();

            $data = $request->except('destinations', 'players');

            $user = request()->user();
            $data['created_by'] = $user->id;
            $data['status_id'] = 1;
            $data['sub_status_id'] = 1;

            $requestModel = RequestModel::create($data);

            if (is_array($request->booking_codes) && count($request->booking_codes) > 0) {
                foreach ($request->booking_codes as $code) {
                    $requestModel->codes()->create(['booking_code' => $code]);
                }
            }

            if (is_array($request->destinations) && count($request->destinations) > 0) {
                foreach ($request->destinations as $destination) {
                    $destination['request_id'] = $requestModel->id;
                    $requestDestinationAdded = RequestDestination::create($destination);
                    if (isset($destination['products']) && is_array($destination['products'])) {
                        foreach ($destination['products'] as $pro) {
                            if (!isset($pro['product_id'])) {
                                continue;
                            }

                            $productData = Product::findOrFail($pro['product_id']);


                            $noOfPlayer = (isset($pro['number_of_players'])) ? $pro['number_of_players'] : null;
                            $notes = (isset($pro['notes'])) ? $pro['notes'] : null;

                            $configure_players_with_tee_times = (isset($pro['configure_players_with_tee_times'])) ? $pro['configure_players_with_tee_times'] : null;

                            $requestProduct = RequestProduct::create([
                                'request_destination_id' => $requestDestinationAdded->id,
                                'product_id' => $productData->id,

                                'name' => $productData->name,

                                'is_package' => $productData->is_package,

                                'service_id' => $productData->service_id,
                                'golf_course_id' => $productData->golf_course_id,

                                'code' => $productData->code,
                                'ref_code' => $productData->ref_code,
                                'tui_ref_code' => $productData->tui_code,

                                'tee_time_id' => $productData->tee_time_id,
                                'hole_id' => $productData->hole_id,

                                'junior' => $productData->junior,
                                'multi_players_only' => $productData->multi_players_only,
                                'buggy' => $productData->buggy,

                                'invoice_handler_id' => $productData->invoice_handler_id,
                                'service_handler_type_id' => $productData->service_handler_type_id,
                                'service_handler_id' => $productData->service_handler_id,

                                'booking_possible_for' => $productData->booking_possible_for,
                                'booking_from_id' => $productData->booking_from_id,

                                'additional_information' => $productData->additional_information,

                                'number_of_players' => $noOfPlayer,
                                'notes' => $notes,

                                'configure_players_with_tee_times' => $configure_players_with_tee_times,

                                'status_id' => '1',

                                'created_by' => $user->id
                            ]);

                            foreach ($productData->details as $item) {
                                RequestProductDetails::create([
                                    'request_product_id' => $requestProduct->id,
                                    'product_id' => $productData->id,
                                    'product_details_id' => $item->id,
                                    'golf_course_id' => $item->golf_course_id,
                                    'type' => $item->type,
                                    'tee_time_id' => $item->tee_time_id,
                                    'min_tee_time_id' => $item->min_tee_time_id,
                                    'max_tee_time_id' => $item->max_tee_time_id,
                                ]);
                            }
                        }
                    }
                }
            }

            if (is_array($request->players) && count($request->players) > 0) {
                foreach ($request->players as $player) {
                    $player['request_id'] = $requestModel->id;
                    RequestPlayer::create($player);
                }
            }

            DB::commit();


            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            // 'travel_agency_id' => 'required|exists:travel_agencies,id',
            // 'phone' => 'required',
            // 'fax' => 'required',
            // 'email' => 'required|email',

            'type_id' => 'required|exists:request_types,id',

            'booking_code' => 'array'

        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $requestModel->update($data);

            if (is_array($request->booking_codes)) {
                $requestModel->codes()->forceDelete();
                foreach ($request->booking_codes as $code) {
                    $requestModel->codes()->create(['booking_code' => $code]);
                }
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_bulk($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            // 'travel_agency_id' => 'required|exists:travel_agencies,id',
            // 'phone' => 'required',
            // 'fax' => 'required',
            // 'email' => 'required|email',

            'type_id' => 'required|exists:request_types,id',

            'booking_code' => 'array'

        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $requestModel->update($data);

            if (is_array($request->booking_codes)) {
                $requestModel->codes()->forceDelete();
                foreach ($request->booking_codes as $code) {
                    $requestModel->codes()->create(['booking_code' => $code]);
                }
            }

            if (is_array($request->destinations) && count($request->destinations) > 0) {
                foreach ($request->destinations as $destination) {
                    $destination['request_id'] = $requestModel->id;
                    $requestDestinationAdded = RequestDestination::create($destination);
                    if (isset($destination['products']) && is_array($destination['products'])) {
                        foreach ($destination['products'] as $pro) {
                            if (!isset($pro['product_id'])) {
                                continue;
                            }

                            $productData = Product::findOrFail($pro['product_id']);

                            $noOfPlayer = (isset($pro['number_of_players'])) ? $pro['number_of_players'] : null;
                            $notes = (isset($pro['notes'])) ? $pro['notes'] : null;

                            $configure_players_with_tee_times = (isset($pro['configure_players_with_tee_times'])) ? $pro['configure_players_with_tee_times'] : null;

                            $requestProduct = RequestProduct::create([
                                'request_destination_id' => $requestDestinationAdded->id,
                                'product_id' => $productData->id,

                                'name' => $productData->name,

                                'is_package' => $productData->is_package,

                                'service_id' => $productData->service_id,
                                'golf_course_id' => $productData->golf_course_id,

                                'code' => $productData->code,
                                'ref_code' => $productData->ref_code,
                                'tui_ref_code' => $productData->tui_code,

                                'tee_time_id' => $productData->tee_time_id,
                                'hole_id' => $productData->hole_id,

                                'junior' => $productData->junior,
                                'multi_players_only' => $productData->multi_players_only,
                                'buggy' => $productData->buggy,

                                'invoice_handler_id' => $productData->invoice_handler_id,
                                'service_handler_type_id' => $productData->service_handler_type_id,
                                'service_handler_id' => $productData->service_handler_id,

                                'booking_possible_for' => $productData->booking_possible_for,
                                'booking_from_id' => $productData->booking_from_id,

                                'additional_information' => $productData->additional_information,

                                'number_of_players' => $noOfPlayer,
                                'notes' => $notes,

                                'configure_players_with_tee_times' => $configure_players_with_tee_times,

                                'status_id' => '1',

                                'created_by' => $user->id
                            ]);

                            foreach ($productData->details as $item) {
                                RequestProductDetails::create([
                                    'request_product_id' => $requestProduct->id,
                                    'product_id' => $productData->id,
                                    'product_details_id' => $item->id,
                                    'golf_course_id' => $item->golf_course_id,
                                    'type' => $item->type,
                                    'tee_time_id' => $item->tee_time_id,
                                    'min_tee_time_id' => $item->min_tee_time_id,
                                    'max_tee_time_id' => $item->max_tee_time_id,
                                ]);
                            }
                        }
                    }
                }
            }

            if (is_array($request->players) && count($request->players) > 0) {
                foreach ($request->players as $player) {
                    $player['request_id'] = $requestModel->id;
                    RequestPlayer::create($player);
                }
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_status($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([

            'status_id' => 'exists:request_statuses,id',
            'sub_status_id' => 'exists:request_sub_statuses,id',

            'is_submit' => 'in:0,1',
            'is_approved' => 'in:0,1',
            'is_cancelled' => 'in:0,1',

        ]);

        try {
            DB::beginTransaction();


            // Send Email Prepare Data
            $emails = [
                'smta0@yahoo.com',
                'aya.ashraf@evolvice.de',
                'muhannad.sinjab@evolvice.de',
                'golfglobe-gap@netzlodern.dev'
            ];

            $userCompanyIDs = [];

            $agencyOperatorEmail = "";
            $golfGlobeEmail = "";

            $ggCompany = Company::where('company_type_id', '1')->first();

            if ($ggCompany) {
                $golfGlobeEmail = $ggCompany->email;
                $userCompanyIDs[] = $ggCompany->id;
            }


            if ($requestModel->travel_agency_id != null) {
                if (isset($requestModel->travelAgency)) {
                    $agencyOperatorEmail = $requestModel->travelAgency->email;
                    $userCompanyIDs[] = $requestModel->travelAgency->company_id;
                }
            } elseif ($requestModel->tour_operator_id != null) {
                if (isset($requestModel->tourOperator)) {
                    $agencyOperatorEmail = $requestModel->tourOperator->email;
                    $userCompanyIDs[] = $requestModel->tourOperator->company_id;
                }
            }
            // End Send Email Prepare Date

            $data = $request->only(
                'status_id',
                'sub_status_id'
            );

            $user = request()->user();

            $data['updated_by'] = $user->id;

            if ($request->is_submit == '1') {
                (new NotificationsHelper($request, $requestModel))->handelRequestNotify();
                $data['sub_status_id'] = '2';
                $data['submit_date'] = date('Y-m-d');
            }

            if ($request->is_approved == '1') {
                $data['status_id'] = '2';
                $data['sub_status_id'] = '5';

                // Send Mail
                foreach ($requestModel->destinations as $destination) {
                    foreach ($destination->products as $product) {
                        if (in_array($product->service_handler_type_id, ['4', '6'])) // Hotel or DMC Handler
                        {
                            // Change Status Of Product and TeeTimes To Redirected
                            foreach ($product->requestTeeTimes as $teeTime) {
                                $teeTime->update([
                                    'status_id' => '2'
                                ]);
                            }
                        } elseif (in_array($product->service_handler_type_id, ['3'])) {
                            foreach ($product->requestTeeTimes as $teeTime) {
                                // Change Status Of TeeTime To Redirected
                                $teeTime->update([
                                    'status_id' => '2'
                                ]);
                            }
                        }

                        // Check if all tee times are redirected, then change the status of the product to redirected
                        $product->update([
                            'status_id' => '2'
                        ]);
                    }
                }

                // check if the status of all products as redirected, then change the status of the request to sys redirect
                $requestModel->update([
                    'sub_status_id' => '5'
                ]);

                (new NotificationsHelper($request, $requestModel))->handelRequestNotify();

                $data['status_id'] = '2';
                $data['sub_status_id'] = '6';
            }

            if ($request->is_cancelled == '1') {
                $data['status_id'] = '3';

                if ($user->details->company) {
                    if ($user->details->company->company_type_id == '1') {
                        $data['sub_status_id'] = '13';
                    } else {
                        $data['sub_status_id'] = '12';
                    }
                } else {
                    $data['sub_status_id'] = '12';
                }
            }

            if ($request->sub_status_id == 11) {
                activity()->withoutLogs(function () use ($requestModel) {
                    $this->store_vouchers($requestModel->id);
                });
            }


            $requestModel->update($data);


            (new NotificationsHelper($request, $requestModel))->handelRequestNotify();

            // $is_approve= $request->is_approved ;
            //     // Send Notification
            //     $notificationUsersData = User::whereHas('details', function($query) use($userCompanyIDs, $is_approve){
            //         $query->whereIn('company_id', $userCompanyIDs);
            //         if ($is_approve) {
            //             $query->whereHas('company', function($q){
            //                 $q->where('company_type_id',2);
            //             });
            //         }
            //     })->get();



            //     if(count($notificationUsersData) > 0)
            //         (new NotificationController())->sendWebNotificationUsers($sub[1], $body[1], $notificationUsersData);

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delegate_client($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'player_id' => 'required|exists:request_players,id',
            'email' => 'required|email',
            'route_url' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $requestPlayer = RequestPlayer::findOrFail($request->player_id);
            $user = request()->user();

            if ($requestModel->travel_agency_id != null) {
                $userCompanyID = $requestModel->travelAgency->company_id;
            } elseif ($requestModel->tour_operator_id != null) {
                $userCompanyID = $requestModel->tourOperator->company_id;
            }

            // Update Requests Data
            $requestModel->update([
                'is_delegate' => '1',
                'delegate_player_id' => $request->player_id,
                'updated_by' => $user->id
            ]);

            // Update Player Data
            $requestPlayer->update([
                'email' => $request->email
            ]);

            $userCheck = User::where('email', $request->email)->first();

            if ($userCheck) {
                $username = $userCheck->username;
                $randPassword = '';
                $link = $request->route_url;
            } else {
                // Create new user with role player
                $randPassword = mt_rand(10000000, 99999999);
                $username = $this->get_unique_username($requestPlayer->first_name, $requestPlayer->last_name);

                $userClient = User::create([
                    'username' => $username,
                    'email' => $request->email,
                    'password' => bcrypt($randPassword)
                ]);

                // User Details
                $userClientDetails = UserDetails::create([
                    'user_id' => $userClient->id,
                    'first_name' => $requestPlayer->first_name,
                    'last_name' => $requestPlayer->last_name,
                    'role_id' => 3,
                    'company_id' => $userCompanyID
                ]);

                // Send Email To User
                $token = \Str::random(60);
                \DB::table('password_resets')->insert([
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
                $link = $request->route_url . '?client_token=' . urlencode($token);
            }


            $title = 'Configure Your Tee Time';
            $body = 'The email has been forwarded to you to configure your Tee Time.' . "<br>" .  'please use the following credentials to login, or click on the link below.' . "<br>";

            if ($randPassword == '') {
                $body .= 'username:' . $username;
            } else {
                $body .= 'username:' . $username . " | password:" . $randPassword;
            }


            $data = [
                'title' => $title,
                'body' => $body,
                'link' => $link
            ];

            \Mail::to($request->email)->send(new RequestClientMail($data));

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delegate_client_link($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'player_id' => 'required|exists:request_players,id',
        ]);

        try {
            DB::beginTransaction();

            $requestPlayer = RequestPlayer::findOrFail($request->player_id);
            $user = request()->user();

            if ($requestModel->travel_agency_id != null) {
                $userCompanyID = $requestModel->travelAgency->company_id;
            } elseif ($requestModel->tour_operator_id != null) {
                $userCompanyID = $requestModel->tourOperator->company_id;
            }

            // Update Requests Data
            $requestModel->update([
                'is_delegate' => '1',
                'delegate_player_id' => $request->player_id,
                'updated_by' => $user->id
            ]);

            $token = \Str::random(60);

            $userClient = User::where('player_id', $request->player_id)->first();
            if (!$userClient) {
                // Create new user with role player
                $randPassword = mt_rand(10000000, 99999999);
                $username = $this->get_unique_username($requestPlayer->first_name, $requestPlayer->last_name);

                $userClient = User::create([
                    'username' => $username,
                    'email' => $request->email,
                    'password' => bcrypt($randPassword),
                    'player_id' => $requestPlayer->id
                ]);

                // User Details
                $userClientDetails = UserDetails::create([
                    'user_id' => $userClient->id,
                    'first_name' => $requestPlayer->first_name,
                    'last_name' => $requestPlayer->last_name,
                    'role_id' => UserDetails::CLIENT_ROLE,
                    'company_id' => $userCompanyID
                ]);
            }

            // Send Email To User
            \DB::table('password_resets')->insert([
                'email' => $userClient->id,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'token' => $token
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function revoke_delegate_client_link($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            // 'player_id' => 'required|exists:request_players,id',
        ]);

        try {
            DB::beginTransaction();

            $user = request()->user();

            $userClient = User::where('player_id', $requestModel->delegate_player_id)->first();
            if ($userClient) {
                $tokenCheck = \DB::table('password_resets')->where('email', $userClient->id)->first();
                if ($tokenCheck) {
                    \DB::table('password_resets')->where('email', $userClient->id)->delete();
                }
            }

            $requestModel->update([
                'is_delegate' => '0',
                'delegate_player_id' => null,
                'updated_by' => $user->id
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delegate_client_link_2($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'player_id' => 'required|exists:request_players,id',
            // 'email' => 'required|email',
            // 'route_url' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $requestPlayer = RequestPlayer::findOrFail($request->player_id);
            $user = request()->user();

            if ($requestModel->travel_agency_id != null) {
                $userCompanyID = $requestModel->travelAgency->company_id;
            } elseif ($requestModel->tour_operator_id != null) {
                $userCompanyID = $requestModel->tourOperator->company_id;
            }

            // Update Requests Data
            $requestModel->update([
                'is_delegate' => '1',
                'delegate_player_id' => $request->player_id,
                'updated_by' => $user->id
            ]);

            // Update Player Data
            $requestPlayer->update([
                'email' => $request->email
            ]);

            $userClient = User::where('player_id', $request->player_id)->first();

            if (!$userClient) {
                // Create new user with role player
                $randPassword = mt_rand(10000000, 99999999);
                $username = $this->get_unique_username($requestPlayer->first_name, $requestPlayer->last_name);

                $userClient = User::create([
                    'username' => $username,
                    'email' => $request->email,
                    'password' => bcrypt($randPassword),
                    'player_id' => $requestPlayer->id
                ]);

                // User Details
                $userClientDetails = UserDetails::create([
                    'user_id' => $userClient->id,
                    'first_name' => $requestPlayer->first_name,
                    'last_name' => $requestPlayer->last_name,
                    'role_id' => 3,
                    'company_id' => $userCompanyID
                ]);
            }

            DB::commit();

            if ($request->device_key) {
                $userClient->deviceKeys()->create(['device_key' => $request->device_key]);
            }

            $userData = new UserFullDataResource($userClient);
            $token = $userClient->createToken((request()->device_name) ? request()->device_name : 'auth-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => $userData,
                'token' => $token
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function send_reminder($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        foreach ($requestModel->destinations as $destination) {
            foreach ($destination->products as $product) {
                foreach ($product->requestTeeTimes as $teeTime) {
                    if ($teeTime->status_id != '4') {
                        // Send Reminder
                        $emails = [
                            'aya.ashraf@evolvice.de',
                            'muhannad.sinjab@evolvice.de',
                            'golfglobe-gap@netzlodern.dev'
                        ];

                        $handlerEmail = '';

                        if ($product->get_service_handler_info()) {
                            $handlerEmail = $product->get_service_handler_info()->email;
                        }

                        $title = 'Request TeeTime Reminder For RequestID #' . $requestModel->id;
                        $body = 'Please Note, This is a reminder for Request ID # ' . $requestModel->id;

                        $emailBody = $body . "<br>" . 'Attached Emails ' . $handlerEmail;

                        $data = [
                            'title' => $title,
                            'body' => $emailBody
                        ];

                        // Send Mail
                        $sub = $requestModel->id . ' | Reminder';
                        (new EmailController())->send($title, $emailBody, $sub, $emails, null, $requestModel->id, 'App\Models\Request');
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
        ]);
    }

    public function store_destinations($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            // 'hotel_id' => 'required|exists:hotels,id',
            // 'arrival_date' => 'required',
            // 'departure_date' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['request_id'] = $requestModel->id;
            $data['created_by'] = $user->id;

            $destination = RequestDestination::create($data);

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);
            $destinationData = new RequestDestinationResource($destination);

            return response()->json([
                'status' => true,
                'destination' => $destinationData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_destinations_bulk($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);
        $validated = $request->validate([
            'destinations' => 'required|array|min:1',
            'destinations.*.city_id' => 'required|exists:cities,id',
            // 'destinations.*.hotel_id' => 'required|exists:hotels,id',
            // 'destinations.*.arrival_date' => 'required',
            // 'destinations.*.departure_date' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $user = request()->user();

            foreach ($request->destinations as $destination) {
                $destination['request_id'] = $requestModel->id;
                $destination['created_by'] = $user->id;

                $destinationDB = RequestDestination::create($destination);
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_destinations($id, Request $request)
    {
        $destination = RequestDestination::findOrFail($id);
        $requestModel = RequestModel::find($destination->request_id);

        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            // 'hotel_id' => 'required|exists:hotels,id',
            // 'arrival_date' => 'required',
            // 'departure_date' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $destination->update($data);
            $destination->products()->forceDelete();
            $requestModel->update([
                'status_id' => '1',
                'sub_status_id' => '1',
            ]);

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);
            $destinationData = new RequestDestinationResource(RequestDestination::findOrFail($destination->id));

            return response()->json([
                'status' => true,
                'destination' => $destinationData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delete_destinations($id, Request $request)
    {
        $destination = RequestDestination::findOrFail($id);

        $request_id = $destination->request_id;

        try {
            DB::beginTransaction();

            foreach ($destination->clients as $client) {
                if ($client->destinations->count() > 1) {
                    DB::table('request_client_destination')
                        ->where('request_destination_id', $destination->id)
                        ->where('request_client_id', $client->id)
                        ->delete();
                } else {
                    $client->forceDelete();
                }
            }

            foreach ($destination->players as $player) {
                if ($player->destinations->count() > 1) {
                    DB::table('request_player_destination')
                        ->where('request_destination_id', $destination->id)
                        ->where('request_player_id', $player->id)
                        ->delete();
                } else {
                    $player->forceDelete();
                }
            }

            $destination->products()->forceDelete();

            $destination->forceDelete();

            DB::commit();

            $requestData = new RequestDetailsResource(RequestModel::find($request_id));

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_clients($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            // 'booking_code' => 'required',
            // 'groups' => 'required',

            'is_leader' => 'required|in:1,0',
            // 'leader_type_id' => 'required',
            // 'leader_company_id' => 'required',
            'destinations' => 'required|array|min:1',
            'destinations.*' => 'exists:request_destinations,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['request_id'] = $requestModel->id;
            $data['created_by'] = $user->id;

            $requestClient = RequestClient::create($data);

            foreach ($request->destinations as $destination) {
                $requestClient->destinations()->attach($destination);
            }

            $requestPlayer = $this->create_new_player($request->all(), $requestModel, $requestClient);

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);
            $clientData = new RequestClientResource($requestClient);

            return response()->json([
                'status' => true,
                'client' => $clientData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_clients_bulk($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'clients' => 'required|array|min:1',
            'clients.*.first_name' => 'required',
            'clients.*.last_name' => 'required',
            // 'booking_code' => 'required',
            // 'groups' => 'required',

            'clients.*.is_leader' => 'required|in:1,0',
            // 'leader_type_id' => 'required',
            // 'leader_company_id' => 'required',
            'clients.*.destinations' => 'required|array|min:1',
            'clients.*.destinations.*' => 'exists:request_destinations,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->clients as $client) {
                $data = $client;

                $user = request()->user();

                $data['request_id'] = $requestModel->id;
                $data['created_by'] = $user->id;

                $requestClient = RequestClient::create($data);

                foreach ($client['destinations'] as $destination) {
                    $requestClient->destinations()->attach($destination);
                }

                $requestPlayer = $this->create_new_player($client, $requestModel, $requestClient);
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_clients($id, Request $request)
    {
        $client = RequestClient::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            // 'booking_code' => 'required',
            // 'groups' => 'required',

            'is_leader' => 'required|in:1,0',
            // 'leader_type_id' => 'required',
            // 'leader_company_id' => 'required',
            'destinations' => 'required|array|min:1',
            'destinations.*' => 'exists:request_destinations,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $client->update($data);
            $client->destinations()->detach();

            foreach ($request->destinations as $destination) {
                $client->destinations()->attach($destination);
            }

            if ($client->player) {
                $this->update_player_data($request, $client->player);
            }

            DB::commit();

            $requestData = new RequestDetailsResource($client->request);
            $clientData = new RequestClientResource(RequestClient::findOrFail($client->id));

            return response()->json([
                'status' => true,
                'client' => $clientData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delete_clients($id, Request $request)
    {
        $client = RequestClient::findOrFail($id);
        $request_id = $client->request_id;

        try {
            DB::beginTransaction();

            $client->destinations()->detach();
            $client->forceDelete();

            DB::commit();

            $requestData = new RequestDetailsResource(RequestModel::find($request_id));

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_players($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            // 'groups' => 'required',
            'hcp' => 'required',
            // 'destinations' => 'required|array|min:1',
            // 'destinations.*' => 'exists:request_destinations,id',
        ]);

        try {
            DB::beginTransaction();

            $requestPlayer = $this->create_new_player($request->all(), $requestModel);

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);
            $playerData = new RequestPlayerResource($requestPlayer);

            return response()->json([
                'status' => true,
                'player' => $playerData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_players_bulk($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'players' => 'required|array|min:1',
            'players.*.first_name' => 'required',
            'players.*.last_name' => 'required',
            'players.*.gender' => 'required',
            // 'groups' => 'required',
            'players.*.hcp' => 'required',
            // 'players.*.destinations' => 'required|array|min:1',
            // 'players.*.destinations.*' => 'exists:request_destinations,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->players as $player) {
                $this->create_new_player($player, $requestModel);
            }


            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function create_new_player($data, $requestModel, $requestClient = null)
    {

        $user = request()->user();

        $data['request_id'] = $requestModel->id;
        $data['created_by'] = $user->id;

        if ($requestClient) {
            $data['client_id'] = $requestClient->id;
        }

        if (!isset($data['gender'])) {
            $data['gender'] = 'male';
        }

        $requestPlayer = RequestPlayer::create($data);

        if (isset($data['destinations'])) {
            foreach ($data['destinations'] as $destination) {
                $requestPlayer->destinations()->attach($destination);
            }
        }

        return $requestPlayer;
    }
    public function update_players($id, Request $request)
    {
        $player = RequestPlayer::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            // 'groups' => 'required',
            'hcp' => 'required',
            // 'destinations' => 'required|array|min:1',
            // 'destinations.*' => 'exists:request_destinations,id',
        ]);

        try {
            DB::beginTransaction();

            $this->update_player_data($request, $player);

            DB::commit();

            $requestData = new RequestDetailsResource($player->request);
            $playerData = new RequestPlayerResource(RequestPlayer::findOrFail($player->id));

            return response()->json([
                'status' => true,
                'player' => $playerData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_player_data($request, $player)
    {
        $data = $request->all();

        $user = request()->user();

        $data['updated_by'] = $user->id;

        $player->update($data);
        $player->destinations()->detach();

        if (isset($request->destinations)) {
            foreach ($request->destinations as $destination) {
                $player->destinations()->attach($destination);
            }
        }
    }
    public function delete_players($id, Request $request)
    {
        $player = RequestPlayer::findOrFail($id);
        $request_id = $player->request_id;

        try {
            DB::beginTransaction();

            $player->destinations()->detach();

            $player->forceDelete();


            DB::commit();

            $requestData = new RequestDetailsResource(RequestModel::findOrFail($request_id));

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function get_products_lock_edit($id)
    {
        $requestProduct = RequestProduct::findOrFail($id);

        return response()->json([
            'status' => true,
            'lock_edit' => $requestProduct->lock_edit
        ]);
    }

    public function store_products($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'request_destination_id' => 'required|exists:request_destinations,id',
            'product_id' => 'required|exists:products,id',
            // 'number_of_players' => 'required',
            'configure_players_with_tee_times' => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $productData = Product::findOrFail($request->product_id);

            $user = request()->user();

            $requestProduct = RequestProduct::create([
                'request_destination_id' => $request->request_destination_id,
                'product_id' => $productData->id,

                'name' => $productData->name,

                'is_package' => $productData->is_package,

                'service_id' => $productData->service_id,
                'golf_course_id' => $productData->golf_course_id,

                'code' => $productData->code,
                'ref_code' => $productData->ref_code,
                'tui_ref_code' => $productData->tui_code,

                'tee_time_id' => $productData->tee_time_id,
                'hole_id' => $productData->hole_id,

                'junior' => $productData->junior,
                'multi_players_only' => $productData->multi_players_only,
                'buggy' => $productData->buggy,

                'invoice_handler_id' => $productData->invoice_handler_id,
                'service_handler_type_id' => $productData->service_handler_type_id,
                'service_handler_id' => $productData->service_handler_id,

                'booking_possible_for' => $productData->booking_possible_for,
                'booking_from_id' => $productData->booking_from_id,

                'additional_information' => $productData->additional_information,

                'number_of_players' => $request->number_of_players,
                'notes' => $request->notes,

                'configure_players_with_tee_times' => $request->configure_players_with_tee_times,

                'status_id' => '1',

                'created_by' => $user->id
            ]);

            foreach ($productData->details as $item) {
                RequestProductDetails::create([
                    'request_product_id' => $requestProduct->id,
                    'product_id' => $productData->id,
                    'product_details_id' => $item->id,
                    'golf_course_id' => $item->golf_course_id,
                    'type' => $item->type,
                    'tee_time_id' => $item->tee_time_id,
                    'min_tee_time_id' => $item->min_tee_time_id,
                    'max_tee_time_id' => $item->max_tee_time_id,
                ]);
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);
            $productDataRes = new RequestProductResource($requestProduct);

            return response()->json([
                'status' => true,
                'product' => $productDataRes,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_products($id, Request $request)
    {
        $requestProduct = RequestProduct::findOrFail($id);

        $validated = $request->validate([
            // 'number_of_players' => 'required',
            'configure_players_with_tee_times' => 'required|in:0,1'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $requestProduct->update($data);

            DB::commit();

            $requestData = new RequestDetailsResource($requestProduct->destination->request);
            $productDataRes = new RequestProductResource($requestProduct);

            return response()->json([
                'status' => true,
                'product' => $productDataRes,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_products_status($id, Request $request)
    {
        $requestProduct = RequestProduct::findOrFail($id);
        $requestModel = $requestProduct->destination->request;

        $validated = $request->validate([
            'status_id' => 'exists:request_product_statuses,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(
                'status_id',
            );

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $requestProduct->update($data);

            $allConfirmed = false;
            if ($request->status_id == "4") {
                foreach ($requestModel->destinations as $destination) {
                    foreach ($destination->products as $productInfo) {
                        if ($productInfo->status_id == "4") {
                            $allConfirmed = true;
                        } else {
                            $allConfirmed = false;
                            break;
                        }
                    }
                }
            }

            if ($allConfirmed) {
                RequestModel::where('id', $requestProduct->destination->request_id)->update([
                    'status_id' => '2',
                    'sub_status_id' => '8'
                ]);
            }

            $allRedirected = false;
            if ($request->status_id == "2") {
                foreach ($requestProduct->requestTeeTimes as $teeTimeInfo) {
                    if (in_array($teeTimeInfo->status_id, ['3', '4'])) {
                        continue;
                    }
                    $teeTimeInfo->update([
                        'status_id' => '2'
                    ]);
                }

                foreach ($requestModel->destinations as $destination) {
                    foreach ($destination->products as $productInfo) {
                        if ($productInfo->status_id == "2") {
                            $allRedirected = true;
                        } else {
                            $allRedirected = false;
                            break;
                        }
                    }
                }

                // Send Email 
                $RedirectTitle = '';
                $redirectBody = $request->body;

                $RedirectData = [
                    'title' => $RedirectTitle,
                    'body' => $redirectBody
                ];

                // Send Email
                if ($request->direct_emails) {
                    foreach ($request->direct_emails as $e) {
                        if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Redirect Email is not valid Email Address',
                            ], 422);
                        }
                    }

                    if ($request->cc_emails && is_array($request->cc_emails)) {
                        foreach ($request->cc_emails as $e) {
                            if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Redirect Email is not valid Email Address',
                                ], 422);
                            }
                        }
                    }


                    (new EmailController())->send($RedirectTitle, $redirectBody, $request->subject, $request->direct_emails, $request->cc_emails, $requestProduct->id, 'App\Models\RequestProduct');

                    $reRedirect = RequestRedirect::create([
                        'request_id' => $requestProduct->destination->request_id,
                        'request_product_id' => $requestProduct->id,

                        'subject' => $request->subject,
                        'body' => $request->body
                    ]);

                    foreach ($request->direct_emails as $e) {
                        RequestRedirectEmail::create([
                            'request_redirect_id' => $reRedirect->id,
                            'email' => $e,
                            'type' => '1'
                        ]);
                    }

                    if ($request->cc_emails && is_array($request->cc_emails)) {
                        foreach ($request->cc_emails as $e) {
                            RequestRedirectEmail::create([
                                'request_redirect_id' => $reRedirect->id,
                                'email' => $e,
                                'type' => '2'
                            ]);
                        }
                    }
                }
            }

            if ($allRedirected) {
                RequestModel::where('id', $requestProduct->destination->request_id)->update([
                    'status_id' => '2',
                    'sub_status_id' => '6'
                ]);
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);


            $userCompanyIDs = [];

            $agencyOperatorEmail = "";
            $golfGlobeEmail = "";

            $ggCompany = Company::where('company_type_id', '1')->first();

            if ($ggCompany) {
                $golfGlobeEmail = $ggCompany->email;
                $userCompanyIDs[] = $ggCompany->id;
            }


            if ($requestModel->travel_agency_id != null) {
                $agencyOperatorEmail = $requestModel->travelAgency->email;
                $userCompanyIDs[] = $requestModel->travelAgency->company_id;
            } elseif ($requestModel->tour_operator_id != null) {
                $agencyOperatorEmail = $requestModel->tourOperator->email;
                $userCompanyIDs[] = $requestModel->tourOperator->company_id;
            }

            $title = 'Request Product Status Changed';
            $body = 'Please Note, The Request Product Status Changed To ' . $requestProduct->status->name . ' For The Request ID #' . $requestModel->id;

            $data = [
                'title' => $title,
                'body' => $body
            ];

            $emails = [
                'aya.ashraf@evolvice.de',
                'muhannad.sinjab@evolvice.de',
                'golfglobe-gap@netzlodern.dev'
            ];

            // Send Email
            $sub = $requestModel->id . ' | Change in Request Product Status';
            (new EmailController())->send($title, $body, $sub, $emails, null, $requestProduct->id, 'App\Models\RequestProduct');

            // Send Notification
            // $userCompanyIDs[] = $requestProduct->serviceHandler->id;
            if ($requestProduct->get_service_handler_info()) {
                $userCompanyIDs[] = $requestProduct->get_service_handler_info()->id;
            }
            $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
                $query->whereIn('company_id', $userCompanyIDs);
            })->get();

            if (count($notificationUsersData) > 0) (new NotificationController())->sendWebNotificationUsers($title, $body, $notificationUsersData);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_products_lock_edit($id, Request $request)
    {
        $requestProduct = RequestProduct::findOrFail($id);

        $validated = $request->validate([
            'lock_edit' => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(
                'lock_edit',
            );

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $requestProduct->update($data);

            DB::commit();

            $requestData = new RequestDetailsResource($requestProduct->destination->request);

            return response()->json([
                'status' => true
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function get_product_status_logs($id)
    {
        $requestProduct = RequestProduct::find($id);

        $logs = Activity::where(function ($query) use ($id) {
            $query->where('subject_type', 'App\Models\RequestProduct')
                ->where('subject_id', $id)
                ->where(function ($q) {
                    $q->where('description', 'created')->orWhere('description', 'updated');
                })
                ->where(function ($q) {
                    $q->where('properties', 'LIKE', '%status.name%');
                });
        })
            ->orWhere(function ($query) use ($id) {
                $query->where('subject_type', 'App\Models\RequestProduct')
                    ->where('subject_id', $id)
                    ->where(function ($q) {
                        $q->where('description', 'updated');
                    })
                    ->where(function ($q) {
                        $q->where('properties', 'not LIKE', '%status.name%');
                    });
            })
            ->orWhere(function ($sub) use ($id) {
                $sub->where('subject_type', 'App\Models\RequestProductTeeTime')
                    ->where('description', 'created')
                    ->where('properties', 'LIKE', '%"request_product_id":' . $id . '%')
                    ->where('properties', 'LIKE', '%"is_parent":0%');
            })
            ->orderBy('id', 'ASC')->get();


        $logsData = RequestProductStatusLogsResource::collection($logs);

        return response()->json([
            'status' => true,
            'logs' => $logsData
        ]);
    }

    public function delete_products($id, Request $request)
    {
        $requestProduct = RequestProduct::findOrFail($id);

        $request_id = $requestProduct->destination->request_id;
        try {
            DB::beginTransaction();

            $requestProduct->requestTeeTimes()->forceDelete();
            $requestProduct->forceDelete();

            DB::commit();

            $requestData = new RequestDetailsResource(RequestModel::findOrFail($request_id));

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function get_tee_time_alternative($id, Request $request)
    {
        $RequestProductTeeTime = RequestProductTeeTime::find($id);

        $alternatives = RequestAlternativeTeeTimeViewResource::collection($RequestProductTeeTime->alternatives);

        return response()->json([
            'status' => true,
            'alternatives' =>  $alternatives
        ]);
    }

    public function store_tee_times($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'request_product_id' => 'required|exists:request_products,id',
            // 'request_product_details_id' => 'exists:request_product_details,id',
            // 'request_player_id' => 'exists:request_players,id',
            'date' => 'required',
            // 'time_from' => 'required',
            // 'time_to' => 'required',
            // 'pref_time' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $productDetailsData = RequestProductDetails::find($request->request_product_details_id);

            $user = request()->user();

            $data = [
                'is_parent' => 1,
                'request_product_id' => $request->request_product_id,
                'request_product_details_id' => $request->request_product_details_id,
                "request_player_id" => $request->request_player_id,

                'date' => $request->date,
                'time_from' => $request->time_from,
                'time_to' => $request->time_to,
                'pref_time' => $request->pref_time,

                'status_id' => '1',

                'created_by' => $user->id
            ];

            if ($productDetailsData) {
                $data['golf_course_id'] = $productDetailsData->golf_course_id;
                $data['type'] = $productDetailsData->type;
                $data['tee_time_id'] = $productDetailsData->tee_time_id;
                $data['min_tee_time_id'] = $productDetailsData->min_tee_time_id;
                $data['max_tee_time_id'] = $productDetailsData->max_tee_time_id;
            }

            $requestTeeTime = RequestProductTeeTime::create($data);

            DB::commit();

            // $request['store_alternative']=1;
            // (new TeeTimeNotificationsHelper($request,$requestTeeTime))->handelTTimeNotify();

            $requestData = new RequestDetailsResource($requestModel);
            $teeTimeData = new RequestTeeTimeResource($requestTeeTime);

            return response()->json([
                'status' => true,
                'tee_time' => $teeTimeData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_tee_times_alternative($id, Request $request)
    {
        $requestParentTeeTime = RequestProductTeeTime::findOrFail($id);

        $validated = $request->validate([
            'request_product_id' => 'required|exists:request_products,id',
            // 'request_product_details_id' => 'exists:request_product_details,id',
            // 'request_player_id' => 'exists:request_players,id',
            'date' => 'required',
            // 'time_from' => 'required',
            // 'time_to' => 'required',
            // 'pref_time' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $productDetailsData = RequestProductDetails::find($request->request_product_details_id);

            $user = request()->user();

            $data = [
                'parent_id' => $requestParentTeeTime->id,
                'request_product_id' => $request->request_product_id,
                'request_product_details_id' => $request->request_product_details_id,
                "request_player_id" => $request->request_player_id,

                'date' => $request->date,
                'time_from' => $request->time_from,
                'time_to' => $request->time_to,
                'pref_time' => $request->pref_time,

                'status_id' => RequestProductTeeTime::REDIRECTED,

                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            if ($productDetailsData) {
                $data['golf_course_id'] = $productDetailsData->golf_course_id;
                $data['type'] = $productDetailsData->type;
                $data['tee_time_id'] = $productDetailsData->tee_time_id;
                $data['min_tee_time_id'] = $productDetailsData->min_tee_time_id;
                $data['max_tee_time_id'] = $productDetailsData->max_tee_time_id;
            }

            $requestTeeTime = RequestProductTeeTime::create($data);

            // Send Email Notification

            $requestModel = $requestParentTeeTime->requestProduct->destination->request;

            $request['store_alternative'] = 1;
            (new TeeTimeNotificationsHelper($request, $requestTeeTime))->handelTTimeNotify();


            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);
            $teeTimeData = new RequestAlternativeTeeTimeResource($requestTeeTime);

            return response()->json([
                'status' => true,
                'tee_time' => $teeTimeData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_tee_times_bulk($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'tee_times' => 'required|array|min:1',

            'tee_times.*.request_product_id' => 'required|exists:request_products,id',
            // 'tee_times.*.request_product_details_id' => 'exists:request_product_details,id',
            // 'tee_times.*.request_player_id' => 'exists:request_players,id',
            // 'tee_times.*.date' => 'required',
            // 'tee_times.*.time_from' => 'required',
            'tee_times.*.time_to' => 'required',
            // 'tee_times.*.pref_time' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $user = request()->user();

            foreach ($request->tee_times as $item) {
                $productDetailsData = RequestProductDetails::find($item['request_product_details_id']);

                $data = $item;

                $data['is_parent'] = 1;
                $data['status_id'] = 1;
                $data['created_by'] = $user->id;

                if ($productDetailsData) {
                    $data['golf_course_id'] = $productDetailsData->golf_course_id;
                    $data['type'] = $productDetailsData->type;
                    $data['tee_time_id'] = $productDetailsData->tee_time_id;
                    $data['min_tee_time_id'] = $productDetailsData->min_tee_time_id;
                    $data['max_tee_time_id'] = $productDetailsData->max_tee_time_id;
                }

                $requestTeeTime = RequestProductTeeTime::create($data);
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_tee_times($id, Request $request)
    {
        $requestTeeTime = RequestProductTeeTime::findOrFail($id);

        $validated = $request->validate([
            // 'request_product_details_id' => 'exists:request_product_details,id',
            // 'request_player_id' => 'exists:request_players,id',
            'date' => 'required',
            // 'time_from' => 'required',
            // 'time_to' => 'required',
            // 'pref_time' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $productDetailsData = RequestProductDetails::find($request->request_product_details_id);

            $user = request()->user();

            $data = [
                'request_product_details_id' => $request->request_product_details_id,
                "request_player_id" => $request->request_player_id,

                'date' => $request->date,
                'time_from' => $request->time_from,
                'time_to' => $request->time_to,
                'pref_time' => $request->pref_time,

                'updated_by' => $user->id
            ];

            if ($productDetailsData) {
                $data['golf_course_id'] = $productDetailsData->golf_course_id;
                $data['type'] = $productDetailsData->type;
                $data['tee_time_id'] = $productDetailsData->tee_time_id;
                $data['min_tee_time_id'] = $productDetailsData->min_tee_time_id;
                $data['max_tee_time_id'] = $productDetailsData->max_tee_time_id;
            }

            $requestTeeTime->update($data);

            DB::commit();

            $requestData = new RequestDetailsResource($requestTeeTime->requestProduct->destination->request);
            $teeTimeData = new RequestTeeTimeResource($requestTeeTime);

            return response()->json([
                'status' => true,
                'tee_time' => $teeTimeData,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_tee_times_status($id, Request $request)
    {
        $requestTeeTime = RequestProductTeeTime::findOrFail($id);
        $requestModel = $requestTeeTime->requestProduct->destination->request;

        $validated = $request->validate([
            'status_id' => 'exists:request_tee_time_statuses,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(
                'status_id',
                'conf_time'
            );

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $requestTeeTime->update($data);

            if ($request->status_id == "4") {
                if ($requestTeeTime->parent_id == null) {
                    foreach ($requestTeeTime->alternatives as $altInfo) {

                        $altInfo->update([
                            'status_id' => '3'
                        ]);
                    }
                } else {

                    $requestTeeTime->parent->update([
                        'status_id' => '3'
                    ]);

                    foreach ($requestTeeTime->parent->alternatives as $altInfo) {
                        if ($altInfo->id == $requestTeeTime->id) {
                            continue;
                        }

                        $altInfo->update([
                            'status_id' => '3'
                        ]);
                    }
                }
            }

            $allProductConfirmed = false;

            if ($request->status_id == "4") {
                foreach ($requestTeeTime->requestProduct->requestTeeTimes as $teeTimeInfo) {
                    if ($teeTimeInfo->status_id == '4' || $teeTimeInfo->get_confirmed_alternative($teeTimeInfo->parent_id) != null || $teeTimeInfo->is_parent_confirmed($teeTimeInfo->parent_id)) {
                        $allProductConfirmed = true;
                    } else {
                        $allProductConfirmed = false;
                        break;
                    }
                }
            }
            if ($allProductConfirmed) {
                $updated_data['status_id'] = '4';
                RequestProduct::find($requestTeeTime->requestProduct->id)->update($updated_data);
            }

            $allConfirmed = RequestModel::whereHas('destinations', function ($q) {
                $q->whereHas('products', function ($q2) {
                    $q2->where('status_id', '<>', 4);
                });
            })->where('id', $requestModel->id)->count();

            if ($allConfirmed == 0) {
                $updated_data['status_id'] = '2';
                $updated_data['sub_status_id'] = '8';
                RequestModel::find($requestModel->id)->update($updated_data);
            }

            $allProductRedirected = false;
            if ($request->status_id == "2") {
                foreach ($requestTeeTime->requestProduct->requestTeeTimes as $teeTimeInfo) {
                    if ($teeTimeInfo->status_id == '2') {
                        $allProductRedirected = true;
                    } else {
                        $allProductRedirected = false;
                        break;
                    }
                }
            }
            if ($allProductRedirected) {
                $updated_data['status_id'] = '2';
                RequestProduct::find($requestTeeTime->requestProduct->id)->update($updated_data);
            }

            $allRedirected = false;
            if ($request->status_id == "2") {
                foreach ($requestModel->destinations as $destination) {
                    foreach ($destination->products as $productInfo) {
                        if ($productInfo->status_id == "2") {
                            $allRedirected = true;
                        } else {
                            $allRedirected = false;
                            break;
                        }
                    }
                }

                // Send Redirect Email
                $RedirectTitle = '';
                $redirectBody = $request->body;

                $RedirectData = [
                    'title' => $RedirectTitle,
                    'body' => $redirectBody
                ];

                // Send Email
                if ($request->direct_emails) {

                    foreach ($request->direct_emails as $e) {
                        if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Redirect Email is not valid Email Address',
                            ], 422);
                        }
                    }

                    if ($request->cc_emails && is_array($request->cc_emails)) {
                        foreach ($request->cc_emails as $e) {
                            if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Redirect Email is not valid Email Address',
                                ], 422);
                            }
                        }
                    }


                    (new EmailController())->send($RedirectTitle, $redirectBody, $request->subject, $request->direct_emails, $request->cc_emails, $requestTeeTime->id, 'App\Models\RequestProductTeeTime');

                    $reRedirect = RequestRedirect::create([
                        'request_id' => $requestModel->id,
                        'request_tee_time_id' => $requestTeeTime->id,

                        'subject' => $request->subject,
                        'body' => $request->body
                    ]);

                    foreach ($request->direct_emails as $e) {
                        RequestRedirectEmail::create([
                            'request_redirect_id' => $reRedirect->id,
                            'email' => $e,
                            'type' => '1'
                        ]);
                    }

                    if ($request->cc_emails && is_array($request->cc_emails)) {
                        foreach ($request->cc_emails as $e) {
                            RequestRedirectEmail::create([
                                'request_redirect_id' => $reRedirect->id,
                                'email' => $e,
                                'type' => '2'
                            ]);
                        }
                    }
                }

                // Update Alternative Tee Time To Be Redirect
                foreach ($requestTeeTime->alternatives as $altTeeTime) {
                    if (!in_array($altTeeTime->status_id, [3, 5])) {
                        $altTeeTime->update([
                            'status_id' => '2',
                            'updated_by' => $user->id
                        ]);
                    }

                    $reRedirect = RequestRedirect::create([
                        'request_id' => $requestModel->id,
                        'request_tee_time_id' => $altTeeTime->id,

                        'subject' => $request->subject,
                        'body' => $request->body
                    ]);

                    foreach ($request->direct_emails as $e) {
                        RequestRedirectEmail::create([
                            'request_redirect_id' => $reRedirect->id,
                            'email' => $e,
                            'type' => '1'
                        ]);
                    }

                    if ($request->cc_emails && is_array($request->cc_emails)) {
                        foreach ($request->cc_emails as $e) {
                            RequestRedirectEmail::create([
                                'request_redirect_id' => $reRedirect->id,
                                'email' => $e,
                                'type' => '2'
                            ]);
                        }
                    }
                }
            }

            if ($allRedirected) {
                $updated_data['status_id'] = '2';
                $updated_data['sub_status_id'] = '6';
                RequestModel::find($requestModel->id)->update($updated_data);
            }


            DB::commit();

            $requestData = new RequestDetailsResource($requestModel);

            (new TeeTimeNotificationsHelper($request, $requestTeeTime))->handelTTimeNotify();

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_tee_times_bulk($id, Request $request)
    {
        $requestProduct = RequestProduct::findOrFail($id);

        $validated = $request->validate([
            'tee_times' => 'required|array|min:1',

            // 'tee_times.*.request_product_id' => 'required|exists:request_products,id',
            // 'tee_times.*.request_product_details_id' => 'exists:request_product_details,id',
            // 'tee_times.*.request_player_id' => 'exists:request_players,id',
            'tee_times.*.date' => 'required',
            // 'tee_times.*.time_from' => 'required',
            // 'tee_times.*.time_to' => 'required',
            // 'tee_times.*.pref_time' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $user = request()->user();

            RequestProductTeeTime::where('request_product_id', $requestProduct->id)->forceDelete();

            foreach ($request->tee_times as $item) {
                $productDetailsData = RequestProductDetails::find($item['request_product_details_id']);

                $data = $item;

                $data['status_id'] = '1';
                $data['created_by'] = $user->id;
                $data['request_product_id'] = $requestProduct->id;

                if ($productDetailsData) {
                    $data['golf_course_id'] = $productDetailsData->golf_course_id;
                    $data['type'] = $productDetailsData->type;
                    $data['tee_time_id'] = $productDetailsData->tee_time_id;
                    $data['min_tee_time_id'] = $productDetailsData->min_tee_time_id;
                    $data['max_tee_time_id'] = $productDetailsData->max_tee_time_id;
                }

                $requestTeeTime = RequestProductTeeTime::create($data);
            }

            DB::commit();

            $requestData = new RequestDetailsResource($requestProduct->destination->request);

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function get_tee_times_status_logs($id)
    {
        $requestProductTeeTime = RequestProductTeeTime::find($id);

        $logs = Activity::where(function ($query) use ($id) {
            $query->where('subject_type', 'App\Models\RequestProductTeeTime')
                ->where('subject_id', $id)
                ->where(function ($q) {
                    $q->where('description', 'created')->orWhere('description', 'updated');
                })
                ->where(function ($q) {
                    $q->where('properties', 'LIKE', '%status.name%');
                });
        })
            ->orWhere(function ($query) use ($id) {
                $query->where('subject_type', 'App\Models\RequestProductTeeTime')
                    ->where('subject_id', $id)
                    ->where(function ($q) {
                        $q->where('description', 'updated');
                    })
                    ->where(function ($q) {
                        $q->where('properties', 'not LIKE', '%status.name%');
                    });
            })
            ->orWhere(function ($sub) use ($id) {
                $sub->where('subject_type', 'App\Models\RequestProductTeeTime')
                    ->where('description', 'created')
                    ->where('properties', 'LIKE', '%"parent_id":' . $id . '%')
                    ->where('properties', 'LIKE', '%"is_parent":0%');
            })
            ->orderBy('id', 'ASC')->get();


        $logsData = RequestTeeTimeStatusLogsResource::collection($logs);

        return response()->json([
            'status' => true,
            'logs' => $logsData
        ]);
    }

    public function delete_tee_times($id, Request $request)
    {
        $requestTeeTime = RequestProductTeeTime::findOrFail($id);

        $request_id = $requestTeeTime->requestProduct->destination->request_id;
        try {
            DB::beginTransaction();

            $requestTeeTime->forceDelete();

            DB::commit();

            $requestData = new RequestDetailsResource(RequestModel::findOrFail($request_id));

            return response()->json([
                'status' => true,
                'request' => $requestData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_comment($id, Request $request)
    {
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'comment' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            $user = request()->user();

            $data['created_by'] = $user->id;
            $data['user_id'] = $user->id;
            $data['commentable_id'] = $requestModel->id;
            $data['commentable_type'] = 'App\Models\Request';

            $comment = Comment::create($data);

            DB::commit();

            $commentData = new RequestCommentResource($comment);

            return response()->json([
                'status' => true,
                'comment' => $commentData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_document($id, Request $request)
    {
        $user = request()->user();
        $requestModel = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'file' => 'required|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except('file');

            $data['created_by'] = $user->id;
            $data['user_id'] = $user->id;
            $data['request_id'] = $requestModel->id;
            $data['date'] = date('Y-m-d');

            if ($request->hasFile('file')) {

                $extention = $request->file->extension();

                $fileName = \Str::random(6) . time() . '.' . $extention;

                $request->file->move(public_path('images/companies'), $fileName);

                $data['file_type'] = $extention;
                $data['file_name'] = $fileName;
            }

            $document = RequestDocument::create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'document' => new RequestDocumentResource(RequestDocument::find($document->id))
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delete($id, Request $request)
    {
        $request = RequestModel::findOrFail($id);

        try {
            DB::beginTransaction();

            $request->delete();

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function get_unique_username($first_name, $last_name)
    {
        $username = $first_name . $last_name;

        $userCheck = User::where('username', $username)->first();

        if ($userCheck) {
            return $this->get_unique_username($first_name, $last_name . mt_rand(100, 999));
        }

        return $username;
    }

    public function get_unique_voucher($code)
    {
        $Check = RequestProductTeeTime::where('voucher_code', $code)->first();

        if ($Check) {
            return $this->get_unique_voucher($code . mt_rand(100, 999));
        }

        return $code;
    }

    public function store_vouchers($id)
    {
        $requestModel = RequestModel::findOrFail($id);

        $destinations = RequestDestination::where('request_id', $id)->pluck('id')->toArray();
        $p = RequestProduct::where('request_destination_id', $destinations)->pluck('id')->toArray();

        $teeTimes = RequestProductTeeTime::whereIn('request_product_id', $p)->get();

        foreach ($teeTimes as $teeTime) {
            $string = mt_rand(1000, 9999) . mt_rand(10000, 99999);

            $code = $this->get_unique_voucher($string);

            $teeTime->update([
                'voucher_code' => $code
            ]);
        }
    }

    public function close_requests()
    {
        $requests = RequestModel::where('sub_status_id', '10')->get();

        foreach ($requests as $requestModel) {
            $requestDs = false;
            foreach ($requestModel->destinations as $destination) {
                if ($destination->arrival_date >= date('Y-m-d', strtotime("-7 days"))) {
                    $requestDs = true;
                } else {
                    $requestDs = false;
                }
            }

            if ($requestDs) {
                $requestModel->update([
                    'sub_status_id' => '14'
                ]);


                $emails = [
                    'aya.ashraf@evolvice.de',
                    'muhannad.sinjab@evolvice.de',
                    'golfglobe-gap@netzlodern.dev'
                ];

                $golfGlobeEmail = "";

                $ggCompany = Company::where('company_type_id', '1')->first();

                if ($ggCompany) {
                    $golfGlobeEmail = $ggCompany->email;
                }

                $title = 'Request Status Changed RequestID #' . $requestModel->id;
                $body = 'Please Note, The Request with ID # ' . $requestModel->id . ' Closed Successfully';

                $emailBody = $body . "<br>" . 'Attached Emails ' . $golfGlobeEmail;

                $data = [
                    'title' => $title,
                    'body' => $emailBody
                ];

                // Send Mail
                $sub = $requestModel->id . ' | Change in Request Status';
                (new EmailController())->send($title, $emailBody, $sub, $emails, null, $requestModel->id, 'App\Models\Request');
            }
        }

        return true;
    }

    public function prepare_filter($request)
    {
        $filter = [];

        if ($request->ref_id) {
            $filter['ref_id'] = $request->ref_id;
        }

        return $filter;
    }

    public function test()
    {
        // DB::beginTransaction();


        $path = base_path() . '/public/backend/seeders/permissions.json';

        $json = json_decode(file_get_contents($path), true);

        $role = Role::find(1);

        foreach ($json['permissions'] as $permission) {
            $permissionData = Permission::create([
                'id' => $permission['id'],
                'name' => $permission['name'],
                'code'  => $permission['code'],
                'description' => $permission['description'],
                'status' => $permission['status'],
                'module_id' => $permission['module_id'],
                'page_id'  => $permission['page_id'],
            ]);

            if ($role) {
                $role->permissions()->save($permissionData);
            }
        }

        // DB::commit();
    }
}
