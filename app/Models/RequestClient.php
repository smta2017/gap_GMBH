<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;

class RequestClient extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;

    protected $fillable = [
        'request_id',

        'first_name',
        'last_name',
        'email',
        'booking_code',
        'groups',

        'is_leader',
        'leader_type_id',
        'leader_company_id',

        'created_by',
        'updated_by',
        'deleted_by'
    ];
    
    public ?string $logName = 'RequestClient';

    public array $logAttributesToIgnore = [ 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public array $logAttributes = [
        '*',
        'leaderType.name',
        'leaderCompany.name'
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
            'request_id' => $this->request_id,
        ]);
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function leaderType()
    {
        return $this->belongsTo(LeaderType::class, 'leader_type_id');
    }

    public function leaderCompany()
    {
        return $this->belongsTo(Company::class, 'leader_company_id');
    }

    public function destinations()
    {
        return $this->belongsToMany(RequestDestination::class, 'request_client_destination', 'request_client_id', 'request_destination_id');
    }

    public function player()
    {
        return $this->hasOne(RequestPlayer::class, 'client_id');
    }
}
