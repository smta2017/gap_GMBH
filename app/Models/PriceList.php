<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceList extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'service_id',
        'price_list_type_id',
        'populate_list_id',
        'markup',
        'status',

        "created_by",
        "updated_by",
        "deleted_by"
    ];

    public function get_all($filter)
    {
        return $this->where($filter)->get();
    }

    public function service()
    {
        return $this->belongsTo(ProductService::class, 'service_id');
    }

    public function priceListType()
    {
        return $this->belongsTo(PriceListType::class, 'price_list_type_id');
    }

    public function populateList()
    {
        return $this->belongsTo(PriceList::class, 'populate_list_id');
    }
}
