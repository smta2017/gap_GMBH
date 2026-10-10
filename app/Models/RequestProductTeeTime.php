<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;

class RequestProductTeeTime extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;



    public const NEW = 1;
    public const REDIRECTED = 2;
    public const REJECTED = 3;
    public const CONFIRMED = 4;
    public const CANCELED = 5;
    public const APPROVED = 6;

    protected $fillable = [
        'is_parent',
        'parent_id',
        'request_product_id',

        'request_product_details_id',
        'golf_course_id',

        "request_player_id",

        'type',
        'tee_time_id',
        'min_tee_time_id',
        'max_tee_time_id',

        'date',
        'time_from',
        'time_to',
        'pref_time',
        'time_margin',
        'conf_time',

        'status_id',
        'voucher_code',
        'not_confirm_mail',
        'not_confirm5d_mail',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public ?string $logName = 'RequestProductTeeTime';

    public array $logAttributesToIgnore = ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public array $logAttributes = [
        '*',
        'golfcourse.name',
        'teeTime.name',
        'minTeeTime.name',
        'maxTeeTime.name',

        'requestPlayer.first_name',
        'requestPlayer.last_name',

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
            'request_id' => $this->requestProduct->destination->request_id,
        ]);
    }

    public function requestProduct()
    {
        return $this->belongsTo(RequestProduct::class, 'request_product_id');
    }

    public function requestPlayer()
    {
        return $this->belongsTo(RequestPlayer::class, 'request_player_id');
    }

    public function requestProductDetails()
    {
        return $this->belongsTo(RequestProductDetails::class, 'request_product_details_id');
    }

    public function golfcourse()
    {
        return $this->belongsTo(GolfCourse::class, 'golf_course_id');
    }

    public function teeTime()
    {
        return $this->belongsTo(TeeTime::class, 'tee_time_id');
    }

    public function minTeeTime()
    {
        return $this->belongsTo(TeeTime::class, 'min_tee_time_id');
    }

    public function maxTeeTime()
    {
        return $this->belongsTo(TeeTime::class, 'max_tee_time_id');
    }

    public function status()
    {
        return $this->belongsTo(RequestTeeTimeStatus::class, 'status_id');
    }

    public function alternatives()
    {
        return $this->hasMany(requestProductTeeTime::class, 'parent_id');
    }

    public function alternativesQuery()
    {
        $user = request()->user();

        if ($user) {
            $userCompanyId = $user->details->company->id;
            $userCompanyTypeId = $user->details->company->company_type_id;
            $userRoleId = $user->details->role_id;

            if (in_array($userCompanyTypeId, ['3'])) {
                // GolfClube
                $golfCoursesUser = $user->childs->whereIn('child_type_id', ['3'])->pluck('child_id')->toArray();

                return $this->alternatives->whereIn('golf_course_id', $golfCoursesUser);
            }
        }

        return $this->alternatives;
    }

    public function parent()
    {
        return $this->belongsTo(requestProductTeeTime::class, 'parent_id');
    }

    public function get_redirect_count()
    {
        return RequestRedirect::where('request_tee_time_id', $this->id)->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdbyuser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function UpdatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function get_confirmed_alternative($parentID = null)
    {

        if ($parentID == null) {
            $teeTimes = $this->alternatives;
        } else {
            $teeTimes = $this->parent->alternatives;
        }

        foreach ($teeTimes as $altInfo) {
            if ($altInfo->status_id == '4') {
                return $altInfo->id;
            }
        }

        return null;
    }

    public function is_parent_confirmed($parentID = null)
    {
        if ($parentID != null) {
            $teeTime = RequestProductTeeTime::find($parentID);

            if ($teeTime && $teeTime->status_id == '4') {
                return true;
            }
        }

        return false;
    }

    public function scopeParents($query)
    {
        $query->whereNull('parent_id');
    }
    public function isParent()
    {
        if (is_null($this->parent)) {
            return true;
        } else {
            return false;
        }
    }

    public function statusLogs()
    {
        $logs = Activity::where(function ($query) {
            $query->where('subject_type', 'App\Models\RequestProductTeeTime')
                ->where('subject_id', $this->id)
                ->where(function ($q) {
                    $q->where('description', 'created')->orWhere('description', 'updated');
                })
                ->where(function ($q) {
                    $q->where('properties', 'LIKE', '%status.name%');
                });
        })
            ->orWhere(function ($query) {
                $query->where('subject_type', 'App\Models\RequestProductTeeTime')
                    ->where('subject_id', $this->id)
                    ->where(function ($q) {
                        $q->where('description', 'updated');
                    })
                    ->where(function ($q) {
                        $q->where('properties', 'not LIKE', '%status.name%');
                    });
            })
            ->orWhere(function ($sub) {
                $sub->where('subject_type', 'App\Models\RequestProductTeeTime')
                    ->where('description', 'created')
                    ->where('properties', 'LIKE', '%"parent_id":' . $this->id . '%')
                    ->where('properties', 'LIKE', '%"is_parent":0%');
            })
            ->orderBy('id', 'ASC')->get();

        return $logs;
    }
}
