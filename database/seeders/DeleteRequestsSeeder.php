<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request;

class DeleteRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Request::withTrashed()->forceDelete(); 
        Request::truncate(); 
    }
}
