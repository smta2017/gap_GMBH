<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyDocument extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'document_type_id',
        'company_id',
        'expire_date',
        'file_name',
        'file_type',
        'is_notify',
        
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function documenttype()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
