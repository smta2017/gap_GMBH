<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Integration extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'api_key',
        'status',
        'expiry_date',

        'company_id',

        'created_by',
        'updated_by',
        'deleted_by',
        'no_expire'
    ];

    public function get_all($filter)
    {
        return $this->where($filter)->get();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
