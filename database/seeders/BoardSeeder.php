<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Board::count() == 0)
        {
            Board::create([
                'name' => 'BB',
                'status' => '1',
            ]);
            Board::create([
                'name' => 'HB',
                'status' => '1',
            ]);
            Board::create([
                'name' => 'FB',
                'status' => '1',
            ]);
            Board::create([
                'name' => 'HB+',
                'status' => '1',
            ]);
            Board::create([
                'name' => 'AL',
                'status' => '1',
            ]);
            Board::create([
                'name' => 'Semi',
                'status' => '1',
            ]);
        }
    }
}
