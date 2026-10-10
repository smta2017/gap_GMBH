<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    use HasFactory;

    public const GG = 1;
    public const TA = 2;
    public const TO = 5;
    public const HO = 4;
    public const DMC = 6;
    public const GC = 3;


    public const AGENCY = [self::TA,self::TO];
    public const SProvider = [self::GC,self::HO,self::DMC];


    protected $fillable = [
        'id',
        'name'
    ];

    public function get_all($whereInList = [])
    {

        if(count($whereInList) == 0) return $this->get();
        return $this->whereIn('id', $whereInList)->get();
    }
}
