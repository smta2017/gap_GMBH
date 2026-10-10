<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'sort'
    ];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function get_all()
    {
        return $this->orderBy('sort', 'ASC')->where('status', '1')->get();
    }
}
