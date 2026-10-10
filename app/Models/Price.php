<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'service_id',
        'price_list_id',
        'product_id',
        'season_id',

        'price',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function priceList()
    {
        return $this->belongsTo(PriceList::class, 'price_list_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
