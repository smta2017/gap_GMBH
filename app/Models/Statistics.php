<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Statistics extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'key',
        'value'
    ];

    public function get_key($key)
    {
        $check = Self::where('key', $key)->first();

        if($check)
        {
            return $check->value;
        }

        return "0";
    }

    public function set_key($key, $value)
    {
        $check = Self::where('key', $key)->first();

        if($check)
        {
            $check->update([
                'value' => $value 
            ]);
        }else{
            Self::create([
                'key' => $key,
                'value' => $value
            ]);
        }

        return true;
    }
}
