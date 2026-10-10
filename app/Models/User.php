<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'lang',
        'player_id',
        'device_key'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function details()
    {
        return $this->hasOne(UserDetails::class);
    }

    public function get_pagination($filter)
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $users = $this->where($filter);
        if(request()->search)
        {
            $search = request()->search;
            $users = $users->whereHas('details', function ($query) use ($search){
                $query->where('first_name', 'LIKE', '%' . $search . '%')->orWhere('last_name', 'LIKE', '%' . $search . '%');
            });
        }
        return $users->paginate($pagination);
    }

    public function setLocale($locale)
    {
        $this->lang = $locale;
        $this->save();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function deviceKeys()
    {
        return $this->hasMany(DeviceKey::class);
    }

    public function childs()
    {
        return $this->hasMany(UserChild::class);
    }
}
