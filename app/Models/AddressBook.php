<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddressBook extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'title',
        'department',
        'company_id',
        'user_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
