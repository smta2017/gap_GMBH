<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'activitieable_id',
        'activitieable_type',
        'start_time',
        'end_time',

        "start_recur",
        "end_recur",
        
        "duration",
        "days_of_week",
        "is_recurring",
        
        'color',
        'type_id'
    ];

    public function activitieable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->belongsTo(ActivityType::class, 'type_id');
    }
}
