<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;

class RequestProductDetails extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;

    protected $fillable = [
        'request_product_id',
        'product_id',
        'product_details_id',
        'golf_course_id',
        'type',
        'tee_time_id',
        'min_tee_time_id',
        'max_tee_time_id',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public ?string $logName = 'RequestProductDetails';

    public array $logAttributesToIgnore = [ 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public array $logAttributes = [
        '*',
        'golfcourse.name',
        'teeTime.name',
        'minTeeTime.name',
        'maxTeeTime.name'
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productDetails()
    {
        return $this->belongsTo(ProductDetails::class, 'product_details_id');
    }

    public function golfcourse()
    {
        return $this->belongsTo(Golfcourse::class, 'golf_course_id');
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
}
