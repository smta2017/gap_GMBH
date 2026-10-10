<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'arrival_date',
        'group_number',
        'number_of_nights',
        'number_of_rounds',
        
        'flight',
        'receive_offer',

        'is_schedule_datetime',
        'schedule_datetime',

        'first_name',
        'last_name',
        'mobile_number',
        'email',

        'city_id',
        'integration_id',
        'user_id',
        'company_id',
        'status_id',

        'source_id',
        'medium_id',
        'compaign',

        'tracking_code',
        'target',
        'airport_name',
        
        'ip_address',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function status()
    {
        return $this->belongsTo(EnquiryStatus::class, 'status_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
    public function medium()
    {
        return $this->belongsTo(Medium::class);
    }

    public function get_all($filter)
    {
        return $this->where($filter)->get();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
