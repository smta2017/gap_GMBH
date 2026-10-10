<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;

class RequestDestination extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;

    protected $fillable = [
        'request_id',
        'city_id',
        'hotel_id',
        'arrival_date',
        'departure_date',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public ?string $logName = 'RequestDestination';

    public array $logAttributesToIgnore = [ 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public array $logAttributes = [
        '*',
        'city.name',
        'hotel.name'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly($this->logAttributes)
        ->logOnlyDirty()
        ->dontLogIfAttributesChangedOnly($this->logAttributesToIgnore)
        ->useLogName($this->logName);
    }
    /**
 * Log all attributes on the model
 */
/**
 * Log all attributes on the model
 */
    
    public function tapActivity(Activity $activity)
    {
        $activity->properties = $activity->properties->merge([
            'request_id' => $this->request_id,
        ]);
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function clients()
    {
        return $this->belongsToMany(RequestClient::class, 'request_client_destination', 'request_destination_id', 'request_client_id');
    }

    public function players()
    {
        return $this->belongsToMany(RequestPlayer::class, 'request_player_destination', 'request_destination_id', 'request_player_id');
    }

    public function products()
    {
        return $this->hasMany(RequestProduct::class, 'request_destination_id');
    }

    public function productsQuery()
    {
        $user = request()->user();

        if($user)
        {
            $userCompanyId = $user->details->company->id;
            $userCompanyTypeId = $user->details->company->company_type_id;
            $userRoleId = $user->details->role_id;
    
            
            if(in_array($userCompanyTypeId, ['4', '6']))
            {
                $HotelDmcUser = $user->childs->whereIn('child_type_id', ['4', '6']);
    
                $HotelDmcUserCompany = [];
                foreach($HotelDmcUser as $u)
                {
                    if($u->child_type_id == '6')
                    {
                        $dmcU = DMC::find($u->child_id);
                        if($dmcU)
                        {
                            $HotelDmcUserCompany[] = $dmcU->company_id;
                        }
                    }
                    if($u->child_type_id == '4')
                    {
                        $hotelU = Hotel::find($u->child_id);
                        if($hotelU)
                        {
                            $HotelDmcUserCompany[] = $hotelU->company_id;
                        }
                    }
                }
    
                // return $this->products->where('service_handler_type_id', $userCompanyTypeId)->whereIn('service_handler_id', $HotelDmcUserCompany);
                // return $this->products->where('service_handler_type_id', $userCompanyTypeId)->where('service_handler_id', $userCompanyId);

                return RequestProduct::where('request_destination_id', $this->id)->where('service_handler_type_id', $userCompanyTypeId)->where(function($q) use($userCompanyId){
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
                                            })->get();

            }
    
            if(in_array($userCompanyTypeId, ['3']))
            {
                return $this->products->where('service_handler_type_id', $userCompanyTypeId);
            }
        }

        return $this->products;
    }
}
