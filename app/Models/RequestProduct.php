<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\HotelResource;
use App\Http\Resources\CityResource;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;

class RequestProduct extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;

    protected $fillable = [
        'request_destination_id',
        'product_id',
        'name',
        
        'is_package',

        'service_id',
        'golf_course_id',

        'code',
        'ref_code',
        'tui_ref_code',

        'tee_time_id',
        'hole_id',
        
        'junior',
        'multi_players_only',
        'buggy',

        'invoice_handler_id',
        'service_handler_type_id',
        'service_handler_id',

        'booking_possible_for',
        'booking_from_id',
        'additional_information',
        
        'number_of_players',
        'notes',

        'configure_players_with_tee_times',
        
        'status_id',
        'lock_edit',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public ?string $logName = 'RequestProduct';

    public array $logAttributesToIgnore = [ 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public array $logAttributes = [
        '*',
        'golfcourse.name',
        'service.name',
        'teeTime.name',
        'hole.name',
        'status.name'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly($this->logAttributes)
        ->logOnlyDirty()
        ->dontLogIfAttributesChangedOnly($this->logAttributesToIgnore)
        ->useLogName($this->logName);
    }
    
    public function tapActivity(Activity $activity)
    {
        $activity->properties = $activity->properties->merge([
            'request_id' => $this->destination->request_id,
        ]);
    }

    public function createdbyuser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    public function requestTeeTimes()
    {
        return $this->hasMany(RequestProductTeeTime::class, 'request_product_id');
    }

    public function requestTeeTimesQuery()
    {
        $user = request()->user();

        if($user)
        {
            $userCompanyId = $user->details->company->id;
            $userCompanyTypeId = $user->details->company->company_type_id;
            $userRoleId = $user->details->role_id;
    
            if(in_array($userCompanyTypeId, ['3']))
            {
                // GolfClube
                $golfCoursesUser = $user->childs->whereIn('child_type_id', ['3'])->pluck('child_id')->toArray();
    
                return $this->requestTeeTimes->whereIn('golf_course_id', $golfCoursesUser);
            }
        }

        return $this->requestTeeTimes;
    }

    public function teeTime()
    {
        return $this->belongsTo(TeeTime::class, 'tee_time_id');
    }

    public function hole()
    {
        return $this->belongsTo(Hole::class, 'hole_id');
    }

    public function destination()
    {
        return $this->belongsTo(RequestDestination::class, 'request_destination_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function invoiceHandler()
    {
        return $this->belongsTo(Company::class, 'invoice_handler_id');
    }

    public function golfcourse()
    {
        return $this->belongsTo(GolfCourse::class, 'golf_course_id');
    }

    public function service()
    {
        return $this->belongsTo(ProductService::class, 'service_id');
    }

    public function serviceHandlerType()
    {
        return $this->belongsTo(CompanyType::class, 'service_handler_type_id');
    }

    public function serviceHandler()
    {
        return $this->belongsTo(Company::class, 'service_handler_id');
    }

    public function status()
    {
        return $this->belongsTo(RequestProductStatus::class, 'status_id');
    }

    public function hotel_data()
    {
        if($this->booking_possible_for == 'Hotel' && $this->booking_from_id != null && Hotel::find($this->booking_from_id) != null)
        {
            return new HotelResource(Hotel::find($this->booking_from_id));
        }else{
            return [];
        }
    }

    public function city_data()
    {
        if($this->booking_possible_for == 'City' && $this->booking_from_id != null && City::find($this->booking_from_id) != null)
        {
            return new CityResource(City::find($this->booking_from_id));
        }else{
            return [];
        }
    }

    public function details()
    {
        return $this->hasMany(RequestProductDetails::class, 'request_product_id');
    }

    public function get_redirect_count()
    {
        return RequestRedirect::where('request_product_id', $this->id)->count();
    }

    public function get_service_handler_info()
    {
        if($this->serviceHandler)
        {
            return $this->serviceHandler;
        }

        if($this->destination && $this->destination->hotel)
            return $this->destination->hotel->company;

        return null;
    }

    public function statusLogs()
    {
        $logs = Activity::where(function($query){
                                $query->where('subject_type', 'App\Models\RequestProduct')
                                                ->where('subject_id', $this->id)
                                                ->where(function($q){
                                                    $q->where('description', 'created')->orWhere('description', 'updated');
                                                })
                                                ->where(function($q){
                                                    $q->where('properties', 'LIKE', '%status.name%');
                                                });
                            })
                            ->orWhere(function($query){
                                $query->where('subject_type', 'App\Models\RequestProduct')
                                                ->where('subject_id', $this->id)
                                                ->where(function($q){
                                                    $q->where('description', 'updated');
                                                })
                                                ->where(function($q){
                                                    $q->where('properties', 'not LIKE', '%status.name%');
                                                });
                            })
                            ->orWhere(function($sub){
                                $sub->where('subject_type', 'App\Models\RequestProductTeeTime')
                                            ->where('description', 'created')
                                            ->where('properties', 'LIKE', '%"request_product_id":'.$this->id.'%')
                                            ->where('properties', 'LIKE', '%"is_parent":0%');
                            })
                            ->orderBy('id', 'ASC')->get();

        return $logs;                
    }
}
