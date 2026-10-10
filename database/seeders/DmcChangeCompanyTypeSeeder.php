<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DMC;
use App\Models\Company;
use DB;

class DmcChangeCompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            $dmc = DMC::get();

            foreach($dmc as $d)
            {
                $co = Company::where('id', $d->company_id)->first();

                if($co->company_type_id != '6')
                {
                    $co->update([
                        'company_type_id' => '6'
                    ]);
                }
            }

            DB::commit();

        }catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}
