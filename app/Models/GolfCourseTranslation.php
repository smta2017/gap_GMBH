<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GolfCourseTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'golf_course_id',
        'language_id',
        'locale',
        'name',
        'website_description',
        'internal_description'
    ];

    public function golfcourse()
    {
        return $this->belongsTo(GolfCourse::class, 'golf_course_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
