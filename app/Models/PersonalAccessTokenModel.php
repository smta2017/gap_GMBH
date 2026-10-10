<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessTokenModel extends Model
{
    use HasFactory;

    protected $table = 'personal_access_tokens';
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'ip',
        'geoip_city_name',
        'browser_name',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function get_pagination()
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $search = request()->search;
        $paToken = $this->whereBetween(\DB::raw('DATE(created_at)'), [request()->date_from, request()->date_to])
            ->where(function ($q)  use ($search) {
                $q->whereHas('user.details', function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('ip', 'LIKE', '%' . $search . '%')
                        ->orWhere('geoip_city_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('browser_name', 'LIKE', '%' . $search . '%');
                })->orWhereHas('user', function ($query) use ($search) {
                    $query->where('username', 'LIKE', '%' . $search . '%');
                });
            });

        return $paToken->paginate($pagination);
    }

    public function tokenable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tokenable_id', 'id');
    }
}
