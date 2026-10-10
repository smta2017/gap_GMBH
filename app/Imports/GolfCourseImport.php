<?php

namespace App\Imports;

use App\Models\GolfCourse;
use Maatwebsite\Excel\Concerns\ToModel;

class GolfCourseImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new GolfCourse([
            //
        ]);
    }
}
