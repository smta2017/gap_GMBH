<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserChild extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'child_id',
        'child_type_id',
    ];

    public function get_child_name()
    {
        switch ($this->child_type_id) {
            case "2":
                $provider = 'App\Models\TravelAgency';
                break;
            case "3":
                $provider = 'App\Models\GolfCourse';
                break;
            case "4":
                $provider = 'App\Models\Hotel';
                break;
            case "5":
                $provider = 'App\Models\TourOperator';
                break;
            case "6":
                $provider = 'App\Models\DMC';
                break;
            default:
        }

        if(!isset($provider))
        {
            return '';
        }
        $child = $provider::where('id', $this->child_id)->first();

        if($child)
        {
            return $child->name;
        }

        return '';
    }

    public function childType()
    {
        return $this->belongsTo(CompanyType::class, 'child_type_id');
    }
}
