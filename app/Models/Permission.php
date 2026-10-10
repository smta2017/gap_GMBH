<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'module_id',
        'page_id'
    ];

    public function get_all($filter)
    {
        return $this->where($filter)->get();
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
    
}
