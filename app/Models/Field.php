<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'type_id',
        'description',
        'is_html',
        'fieldable_id',
        'fieldable_type',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function fieldable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->belongsTo(FieldType::class, 'type_id');
    }

    public function translations()
    {
        return $this->hasMany(FieldTranslation::class);
    }
}
