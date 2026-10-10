<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;


class Request extends Model
{
    use SoftDeletes, HasFactory,LogsActivity;

    public const FIRST_CREATED_STATUS ='Created (Draft)';
    
    public const InComplete =1;
    public const SUBMITED_STATUS =2;
    public const APPROVED_STATUS =5;
    public const SYS_REDIRECTED =6;
    public const CONFIRMED_STATUS =11;

    protected $fillable = [
        'company_id',
        'travel_agency_id',
        'tour_operator_id',
        'ref_id',
        'tui_ref_code',
        'group_code',
        'tui_params',
        'phone',
        'fax',
        'email',

        'type_id',

        'status_id',
        'sub_status_id',

        'is_delegate',
        'delegate_client_id',
        'delegate_player_id',
        'is_client_submit',
        
        'submit_date',
        'notes',
        'not_submit_mail',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public ?string $logName = 'Request';
    
    public array $logAttributes = [
        '*',
        'travelAgency.name',
        'tourOperator.name',
        'type.name',
        'status.name',
        'subStatus.name'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly($this->logAttributes)
        ->logOnlyDirty()
        ->useLogName($this->logName);
    }
    //override log props
    public function tapActivity(Activity $activity,string $eventName)
    {
        if($eventName=='created'){
            $data = json_decode($activity->attributes['properties'],\true);
            $data['attributes']['status.name']= '';
            $data['attributes']['subStatus.name']= self::FIRST_CREATED_STATUS;
            $activity->attributes['properties'] = \json_encode($data,JSON_FORCE_OBJECT);
        }
        if($eventName=='updated'){
            $data = json_decode($activity->attributes['properties'],\true);
            if(isset($data['old']['subStatus.name']) && $data['old']['subStatus.name']=='InComplete') {
                $data['old']['subStatus.name']= self::FIRST_CREATED_STATUS;
            }
            $activity->attributes['properties'] = \json_encode($data,JSON_FORCE_OBJECT);
        }
    }
    
    public static function userCompanyDetails()
    {
        $user = request()->user();

      return  [
            'userCompanyId' => $user->details->company->id,
            'userCompanyTypeId' => $user->details->company->company_type_id,
            'userRoleId' => $user->details->role_id
      ];
    }

    public function getGGEmail()
    {
        $ggCompany = Company::where('company_type_id', '1')->first();
        
        if($ggCompany)
        {
            $golfGlobeEmail = $ggCompany->email;
        }

        return $golfGlobeEmail;
    }

    public function getGGId()
    {
        $ggCompany = Company::where('company_type_id', '1')->first();
        
        if($ggCompany)
        {
            $userCompanyIDs[] = $ggCompany->id;
        }

        return $userCompanyIDs;
    }


    public function getAgencyOperatorsEmail()
    {
        if($this->travel_agency_id != null)
        {
            if(isset($this->travelAgency))
            {
                $agencyOperatorEmail = $this->travelAgency->email;
            }
        }elseif($this->tour_operator_id != null)
        {
            if(isset($this->tourOperator))
            {
                $agencyOperatorEmail = $this->tourOperator->email;
            }
        }
        return $agencyOperatorEmail;
    }

    public function getAgencyOperatorsCompanyIds()
    {
        if($this->travel_agency_id != null)
        {
            if(isset($this->travelAgency))
            {
                $userCompanyIDs[] = $this->travelAgency->company_id;
            }
        }elseif($this->tour_operator_id != null)
        {
            if(isset($this->tourOperator))
            {
                $userCompanyIDs[] = $this->tourOperator->company_id;
            }
        }
        return $userCompanyIDs;
    }

    public function get_pagination()
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $order = (in_array(request()->order, ['ASC', 'DESC'])) ? request()->order : 'DESC';
        $orderBy = (request()->input('order_by')) ? request()->input('order_by') : 'request_id';

        $user = request()->user();

        $userCompanyId = $user->details->company->id;
        $userCompanyTypeId = $user->details->company->company_type_id;
        $userRoleId = $user->details->role_id;
        // $filter = [];
        // if(request()->request_id)
        // {
        //     array_push($filter, array('id',  request()->request_id ));
        // }
        // if(request()->request_ref_id)
        // {
        //     array_push($filter, array('ref_id', 'LIKE', '%' . request()->request_ref_id . '%' ));
        // }
        // if(request()->company_id)
        // {
        //     array_push($filter, array('company_id',  request()->company_id ));
        // }
        // if(request()->travel_agency_id)
        // {
        //     array_push($filter, array('travel_agency_id',  request()->travel_agency_id ));
        // }
        // if(request()->status_id)
        // {
        //     array_push($filter, array('status_id',  request()->status_id ));
        // }
        // if(request()->sub_status_id)
        // {
        //     array_push($filter, array('sub_status_id',  request()->sub_status_id ));
        // }
        // if(request()->date)
        // {
        //     array_push($filter, array('submit_date',  request()->date ));
        // }

        $travelAgencySearch = [];
        if(request()->travel_agency_ref_id)
        {
            array_push($travelAgencySearch, array('ref_id', 'LIKE', '%' . request()->travel_agency_ref_id . '%' ));
        }
        if(request()->travel_agency_name)
        {
            array_push($travelAgencySearch, array('name', 'LIKE', '%' . request()->travel_agency_name . '%' ));
        }

        $destinationSearch = [];
        if(request()->arrival_date)
        {
            array_push($destinationSearch, array('arrival_date',  request()->arrival_date ));
        }
        if(request()->departure_date)
        {
            array_push($destinationSearch, array('departure_date',  request()->departure_date ));
        }
        if(request()->city_id)
        {
            array_push($destinationSearch, array('city_id', 'LIKE', '%' . request()->city_id . '%' ));
        }
        if(request()->hotel_id)
        {
            array_push($destinationSearch, array('hotel_id', 'LIKE', '%' . request()->hotel_id . '%' ));
        }

        $hotelSearch = [];
        if(request()->hotel_name)
        {
            array_push($hotelSearch, array('name', 'LIKE', '%' . request()->hotel_name . '%' ));
        }
        if(request()->hotel_ref_id)
        {
            array_push($hotelSearch, array('ref_id', 'LIKE', '%' . request()->hotel_ref_id . '%' ));
        }

        $citySearch = [];
        if(request()->city_name)
        {
            array_push($citySearch, array('name', 'LIKE', '%' . request()->city_name . '%' ));
        }
        if(request()->city_code)
        {
            array_push($citySearch, array('code', 'LIKE', '%' . request()->city_code . '%' ));
        }

        $countrySearch = [];
        if(request()->country_name)
        {
            array_push($countrySearch, array('name', 'LIKE', '%' . request()->country_name . '%' ));
        }
        if(request()->country_code)
        {
            array_push($countrySearch, array('code', 'LIKE', '%' . request()->country_code . '%' ));
        }

        $regionSearch = [];
        if(request()->region_name)
        {
            array_push($regionSearch, array('name', 'LIKE', '%' . request()->region_name . '%' ));
        }
        if(request()->region_code)
        {
            array_push($regionSearch, array('code', 'LIKE', '%' . request()->region_code . '%' ));
        }

        $productSearch = [];
        if(request()->product_id)
        {
            array_push($productSearch, array('product_id', 'LIKE', '%' . request()->product_id . '%' ));
        }
        if(request()->product_name)
        {
            array_push($productSearch, array('name', 'LIKE',  '%' . request()->product_name . '%' ));
        }
        if(request()->product_code)
        {
            array_push($productSearch, array('code', 'LIKE',  '%' . request()->product_code . '%' ));
        }
        if(request()->product_ref_code)
        {
            array_push($productSearch, array('ref_code', 'LIKE',  '%' . request()->product_ref_code . '%' ));
        }
        if(request()->product_tui_ref_code)
        {
            array_push($productSearch, array('tui_ref_code', 'LIKE',  '%' . request()->product_tui_ref_code . '%' ));
        }
        if(request()->handler_type_id)
        {
            array_push($productSearch, array('service_handler_type_id', 'LIKE', '%' . request()->handler_type_id . '%'));
        }

        $teeTimeSearch = [];
        if(request()->tee_time_id)
        {
            array_push($teeTimeSearch, array('id',  'LIKE', '%' . request()->tee_time_id . '%' ));
        }
        if(request()->tee_time_date)
        {
            array_push($teeTimeSearch, array('date',  request()->tee_time_date ));
        }

        $results = $this;
        // Request Permissions Validations
        if(in_array($userCompanyTypeId, CompanyType::SProvider))
        {
            // Hotel Or DMC Or GC
            array_push($productSearch, array('service_handler_type_id', $userCompanyTypeId));
            $results = $results->where('sub_status_id','=', self::SYS_REDIRECTED);
        }

        // if(in_array($userCompanyTypeId, ['3']))
        // {
        //     // Golf Clube
        //     array_push($productSearch, array('service_handler_type_id', $userCompanyTypeId));
        // }

        $golfCourseSearch = [];
        if(request()->golf_course_name)
        {
            array_push($golfCourseSearch, array('name', 'LIKE',  '%' . request()->golf_course_name . '%' ));
        }

        $playerSearch = [];
        // if(request()->booking_number)
        // {
        //     array_push($clientSearch, array('booking_code', 'LIKE', '%' . request()->booking_number . '%'));
        // }
        if(request()->first_name)
        {
            array_push($playerSearch, array('first_name', 'LIKE', '%' . request()->first_name . '%'));
        }
        if(request()->last_name)
        {
            array_push($playerSearch, array('last_name', 'LIKE', '%' . request()->last_name . '%'));
        }
        
        $productDetailsSearch = [];


        if(request()->request_id)
        {
            $results = $results->where('id', 'LIKE', '%' . request()->request_id . '%');
        }
        if(request()->request_ref_id)
        {
            $results = $results->orWhere('ref_id', 'LIKE', '%' . request()->request_ref_id . '%');
        }
        if(request()->request_tui_ref_code)
        {
            $results = $results->orWhere('tui_ref_code', 'LIKE', '%' . request()->request_tui_ref_code . '%');
        }
        if(request()->company_id)
        {
            $results = $results->orWhere('company_id',  request()->company_id);
        }
        if(request()->travel_agency_id)
        {
            $results = $results->orWhere('travel_agency_id',  'LIKE', '%' . request()->travel_agency_id . '%');
        }
        if(request()->tour_operator_id)
        {
            $results = $results->orWhere('tour_operator_id',  'LIKE', '%' . request()->tour_operator_id . '%');
        }
        if(request()->status_id)
        {
            $results = $results->orWhere('status_id',  request()->status_id);
        }
        if(request()->sub_status_id)
        {
            $results = $results->orWhere('sub_status_id',  request()->sub_status_id);
        }
        if(request()->date)
        {
            $results = $results->orWhereDate('created_at',  request()->date);
        }
        if(request()->request_month)
        {
            $results = $results->whereMonth('submit_date',  request()->request_month)->whereYear('submit_date', date('Y'));
        }


        // Request Permissions Validations
        if($userCompanyTypeId == "2" && $userRoleId != "3")
        {
            // Travel Agency
            $results = $results->whereIn('travel_agency_id', $user->childs->pluck('child_id'));
        }
        if($userCompanyTypeId == "5")
        {
            // Tour Operator
            $operatorsUser = TourOperator::where('company_id', $userCompanyId)->pluck('id')->toArray();
            $results = $results->whereIn('tour_operator_id', $operatorsUser);
        } 
        if($userRoleId == "3")
        {
            // user client role
            $delegatePlayer = RequestPlayer::where('id', $user->player_id)->first();

            if($delegatePlayer)
            {
                $results = $results->where('delegate_player_id', $delegatePlayer->id)->where('status_id', '1')->where('sub_status_id', '1');
            }
        }

        if(count($travelAgencySearch) > 0)
        {
            $results = $results->whereHas('travelAgency', function($query) use ($travelAgencySearch, $order, $orderBy){
                $query->where($travelAgencySearch);

                if($orderBy == 'travel_agency_ref_id')
                {
                    $query->orderBy('ref_id', $order);
                }
            });
        }

        if(request()->booking_number)
        {
            $bookingNumber = request()->booking_number;
            $codesID = BookingCode::where('booking_code', 'LIKE', '%' . $bookingNumber . '%')->where('codeable_type', 'App\Models\Request')->pluck('codeable_id')->toArray();
            $results = $results->whereIn('id', $codesID);
        }

        if( count($destinationSearch) > 0 
            || count($productSearch) > 0 
            || count($teeTimeSearch) > 0 
            || count($golfCourseSearch) > 0 
            || count($hotelSearch) > 0 
            || count($citySearch) > 0 
            || count($regionSearch) > 0 
            || count($countrySearch) > 0 
            || in_array($userCompanyTypeId, ['3'
            || request()->handler_name])
        )
        {
            $results = $results->whereHas('destinations', function($query) use (
                $destinationSearch, 
                $productSearch, 
                $teeTimeSearch, 
                $golfCourseSearch,
                $productDetailsSearch,
                $hotelSearch, 
                $citySearch, 
                $regionSearch, 
                $countrySearch,
                $order,
                $orderBy,
                $userCompanyTypeId,
                $userCompanyId,
                $user
                ){

                    $query->where($destinationSearch);
     
                    if(count($productSearch) > 0 || count($teeTimeSearch) > 0 || count($golfCourseSearch) > 0 || in_array($userCompanyTypeId, ['3', '4', '6']) || request()->handler_name)
                    {
                        $query->whereHas('products', function($q) use ($productSearch, $teeTimeSearch, $golfCourseSearch, $productDetailsSearch, $userCompanyTypeId, $userCompanyId, $user){
                            $q->where($productSearch);
                            
                            // Request Permissions Validations
                            if(in_array($userCompanyTypeId, ['4', '6']))
                            {

                                $x=22;
                                // $HotelDmcUser = $user->childs->whereIn('child_type_id', ['4', '6']);

                                // $HotelDmcUserCompany = [];
                                // foreach($HotelDmcUser as $u)
                                // {
                                //     if($u->child_type_id == '6')
                                //     {
                                //         $dmcU = DMC::find($u->child_id);
                                //         if($dmcU)
                                //         {
                                //             $HotelDmcUserCompany[] = $dmcU->company_id;
                                //         }
                                //     }
                                //     if($u->child_type_id == '4')
                                //     {
                                //         $hotelU = Hotel::find($u->child_id);
                                //         if($hotelU)
                                //         {
                                //             $HotelDmcUserCompany[] = $hotelU->company_id;
                                //         }
                                //     }
                                // }

                                // $q->whereIn('service_handler_id', $HotelDmcUserCompany);

                                // $q->where(function($handlerQuery) use ($HotelDmcUserCompany){
                                //     $handlerQuery->where('service_handler_id', '!=', null)->whereIn('service_handler_id', $HotelDmcUserCompany);
                                // })->orWhere(function($handlerQuery) use ($HotelDmcUserCompany){
                                //     $handlerQuery->where('service_handler_id', null)->whereHas('destination', function($destinationQuery) use ($HotelDmcUserCompany){
                                //         $destinationQuery->whereHas('hotel', function($hotelQuery) use ($HotelDmcUserCompany){
                                //             $hotelQuery->whereHas('company', function($companyQuery) use ($HotelDmcUserCompany){
                                //                 $companyQuery->whereIn('id', $HotelDmcUserCompany);
                                //             });
                                //         });
                                //     });
                                // });

                                $q->where(function($handlerQuery) use ($userCompanyId){
                                    $handlerQuery->where('service_handler_id', '!=', null)->where('service_handler_id', $userCompanyId);
                                })->orWhere(function($handlerQuery) use ($userCompanyId){
                                    $handlerQuery->where('service_handler_id', null)->whereHas('destination', function($destinationQuery) use ($userCompanyId){
                                        $destinationQuery->whereHas('hotel', function($hotelQuery) use ($userCompanyId){
                                            $hotelQuery->whereHas('company', function($companyQuery) use ($userCompanyId){
                                                $companyQuery->where('id', $userCompanyId);
                                            });
                                        });
                                    });
                                });
                            }
                            
                            // Request Permissions Validations
                            if(in_array($userCompanyTypeId, ['3']))
                            {
                                // GolfClube
                                $golfCoursesUser = $user->childs->whereIn('child_type_id', ['3'])->pluck('child_id')->toArray();

                                // $golfCoursesUser = GolfCourse::where('company_id', $userCompanyId)->pluck('id')->toArray();

                                // $q->whereIn('golf_course_id', $golfCoursesUser)->orWhereHas('requestTeeTimes', function($sub) use ($golfCoursesUser){
                                //     $sub->whereIn('golf_course_id', $golfCoursesUser);
                                // });

                                $q->where(function($qq) use($golfCoursesUser){
                                    $qq->whereIn('golf_course_id', $golfCoursesUser)->orWhereHas('requestTeeTimes', function($sub) use ($golfCoursesUser){
                                        $sub->whereIn('golf_course_id', $golfCoursesUser);
                                    });
                                });
                            }

                            // if(request()->handler_name)
                            // {
                            //     $handlerName = request()->handler_name;
                            //     $q->whereHas('get_service_handler_info', function($sub) use($handlerName){
                            //         $sub->where('name', 'LIKE', '%' . $handlerName . '%');
                            //     });
                            // }

                            if(count($teeTimeSearch) > 0)
                            {
                                $q->whereHas('requestTeeTimes', function ($sub) use($teeTimeSearch){
                                    $sub->where($teeTimeSearch);
                                });
                            }

                            if(count($golfCourseSearch) > 0)
                            {
                                $q->whereHas('requestTeeTimes.golfcourse', function($sub) use ($golfCourseSearch){
                                    $sub->where($golfCourseSearch);
                                });
                            }

                            if(count($productDetailsSearch) > 0)
                            {
                                $q->whereHas('details', function ($sub) use ($productDetailsSearch){
                                    $sub->where($productDetailsSearch);
                                });
                            }
                        });
                    }

                    if(count($hotelSearch) > 0)
                    {
                        $query->orWhereHas('hotel', function($q) use ($hotelSearch, $order, $orderBy){
                            $q->where($hotelSearch);
    
                            if($orderBy == 'hotel_name')
                            {
                            $q->orderBy('name', $order);
                            }
                        });
                    }

                    if(count($citySearch) > 0 || count($regionSearch) > 0 || count($countrySearch) > 0)
                    {
                        $query->orWhereHas('city', function($q) use ($citySearch, $regionSearch, $countrySearch, $order, $orderBy){
                            $q->where($citySearch);
    
                            $q->orWhereHas('region', function($sub) use ($regionSearch){
                                $sub->where($regionSearch);
                            });
    
                            $q->orWhereHas('country', function($sub) use ($countrySearch, $order, $orderBy){
                                $sub->where($countrySearch);
    
                                if($orderBy == 'country_code')
                                {
                                    $sub->orderBy('code', $order);
                                }
                            });
                        });
                    }

                });
        }


        if(count($playerSearch) > 0)
        {
            $results = $results->whereHas('players', function($query) use ($playerSearch){
                $query->where($playerSearch);
            });
        }

        
        if($orderBy == 'request_id')
        {
            $results = $results->orderBy('id', $order);
        }

        if($orderBy == 'request_ref_id')
        {
            $results = $results->orderBy('ref_id', $order);
        }

        if($orderBy == 'date')
        {
            $results = $results->orderBy('submit_date', $order);
        }

        
        return $results->paginate($pagination);
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdbyuser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function travelAgency()
    {
        return $this->belongsTo(TravelAgency::class, 'travel_agency_id');
    }

    public function tourOperator()
    {
        return $this->belongsTo(TourOperator::class, 'tour_operator_id');
    }

    public function type()
    {
        return $this->belongsTo(RequestType::class, 'type_id');
    }

    public function status()
    {
        return $this->belongsTo(RequestStatus::class, 'status_id');
    }
    public function subStatus()
    {
        return $this->belongsTo(RequestSubStatus::class, 'sub_status_id');
    }

    public function destinations()
    {
        return $this->hasMany(RequestDestination::class, 'request_id');
    }

    public function clients()
    {
        return $this->hasMany(RequestClient::class, 'request_id');
    }

    public function players()
    {
        return $this->hasMany(RequestPlayer::class, 'request_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function documents()
    {
        return $this->hasMany(RequestDocument::class, 'request_id');
    }

    public function codes()
    {
        return $this->morphMany(BookingCode::class, 'codeable');
    }

    public function teeTimes()
    {
        $teeTimes = RequestProductTeeTime::join('request_products', 'request_products.id', 'request_product_tee_times.request_product_id')
                                        ->join('request_destinations', 'request_products.request_destination_id', 'request_destinations.id')
                                        ->join('requests', 'requests.id', 'request_destinations.request_id')
                                        // ->where('requests.id', $this->id)
                                        ->get();    
        return $teeTimes;
    }

    public function get_teeTimes()
    {
        $teeTimes = RequestProductTeeTime::whereHas('requestProduct.destination', function ($q) {
                $q->where('request_id', $this->id);
        })->get();
        return $teeTimes;
    }
    public function statusLogs()
    {
        $logs = Activity::where('subject_type', 'App\Models\Request')
                    ->where('subject_id', $this->id)
                    ->where(function($q){
                        $q->where('description', 'created')->orWhere('description', 'updated');
                    })
                    ->where(function($q){
                        $q->where('properties', 'LIKE', '%status.name%')
                            ->orWhere('properties', 'LIKE', '%subStatus.name%');
                    })
                    ->orderBy('id', 'ASC')->get();

        return $logs;
    }

    public function get_delegate_player_token()
    {
        $user = User::where('player_id', $this->delegate_player_id)->first();
        if($user)
        {
            $token = \DB::table('password_resets')->where('email', $user->id)->first();  
            if($token)
            {
                return $token->token;
            }
        }

        return null;
    }
}
