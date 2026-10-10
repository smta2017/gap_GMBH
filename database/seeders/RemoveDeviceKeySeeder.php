<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeviceKey;

class RemoveDeviceKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DeviceKey::query()->forceDelete();
        \DB::statement("ALTER TABLE device_keys AUTO_INCREMENT =  1");

    }
}
