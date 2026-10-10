<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetails extends Model
{
    use SoftDeletes, HasFactory;

    public const CLIENT_ROLE = 4;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'fax',
        'title',
        'department',
        'role_id',
        'lang',
        'company_id',
        'address_book_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
