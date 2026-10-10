<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'module_id',
        'status',
        'sort'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function get_all()
    {
        $module_id = request()->input('module_id');

        return $this->where('status', '1')->when($module_id, function($query) use ($module_id){
             return $query->where('module_id', $module_id);
        })->get();
    }
}
