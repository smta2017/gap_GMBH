<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Page;

class ModulePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if(Module::count() == 0)
        {
            Module::create([
                'id' => '1',
                'name' => 'Dashboard',
                'sort' => '1'
            ]);

            

            Module::create([
                'id' => '2',
                'name' => 'Enquiry',
                'sort' => '2'
            ]);

            Page::create([
                'name' => 'Enquiry',
                'module_id' => '2',
                'sort' => '1'
            ]);

            Module::create([
                'id' => '3',
                'name' => 'Request',
                'sort' => '3'
            ]);

            Module::create([
                'id' => '4',
                'name' => 'Booking',
                'sort' => '4'
            ]);

            Module::create([
                'id' => '5',
                'name' => 'Golf Course',
                'sort' => '5'
            ]);

            Page::create([
                'name' => 'Courses',
                'module_id' => '5',
                'sort' => '1'
            ]);

            Page::create([
                'name' => 'Calender',
                'module_id' => '5',
                'sort' => '2'
            ]);

            Module::create([
                'id' => '6',
                'name' => 'Hotels',
                'sort' => '6'
            ]);

            Page::create([
                'name' => 'Hotels',
                'module_id' => '6',
                'sort' => '1'
            ]);

            Module::create([
                'id' => '7',
                'name' => 'DMC',
                'sort' => '6'
            ]);

            Page::create([
                'name' => 'DMC',
                'module_id' => '7',
                'sort' => '1'
            ]);

            Module::create([
                'id' => '8',
                'name' => 'Tour Operators',
                'sort' => '8'
            ]);

            Page::create([
                'name' => 'Tour Operators',
                'module_id' => '8',
                'sort' => '1'
            ]);

            Module::create([
                'id' => '9',
                'name' => 'Travel Agencies',
                'sort' => '9'
            ]);

            Page::create([
                'name' => 'Travel Agencies',
                'module_id' => '9',
                'sort' => '1'
            ]);

            Module::create([
                'id' => '10',
                'name' => 'Product Setup',
                'sort' => '10'
            ]);

            Page::create([
                'name' => 'Services',
                'module_id' => '10',
                'sort' => '1'
            ]);
            Page::create([
                'name' => 'Seasons',
                'module_id' => '10',
                'sort' => '2'
            ]);
            Page::create([
                'name' => 'GFP',
                'module_id' => '10',
                'sort' => '3'
            ]);
            Page::create([
                'name' => 'Hotel Products',
                'module_id' => '10',
                'sort' => '4'
            ]);


            Module::create([
                'id' => '11',
                'name' => 'Payment',
                'sort' => '11'
            ]);

            Module::create([
                'id' => '12',
                'name' => 'Company',
                'sort' => '12'
            ]);

            Page::create([
                'name' => 'Company Profile',
                'module_id' => '12',
                'sort' => '1'
            ]);
            Page::create([
                'name' => 'Documents',
                'module_id' => '12',
                'sort' => '2'
            ]);

            Module::create([
                'id' => '13',
                'name' => 'Destinations',
                'sort' => '13'
            ]);

            Page::create([
                'name' => 'Destinations',
                'module_id' => '13',
                'sort' => '1'
            ]);
            Page::create([
                'name' => 'Regions',
                'module_id' => '13',
                'sort' => '2'
            ]);
            Page::create([
                'name' => 'Countries',
                'module_id' => '13',
                'sort' => '3'
            ]);
            Page::create([
                'name' => 'Cities',
                'module_id' => '13',
                'sort' => '4'
            ]);

            Module::create([
                'id' => '14',
                'name' => 'Administrations',
                'sort' => '14'
            ]);

            Page::create([
                'name' => 'Companies',
                'module_id' => '14',
                'sort' => '1'
            ]);
            Page::create([
                'name' => 'Users',
                'module_id' => '14',
                'sort' => '2'
            ]);
            Page::create([
                'name' => 'Roles',
                'module_id' => '14',
                'sort' => '3'
            ]);
            Page::create([
                'name' => 'Permissions',
                'module_id' => '14',
                'sort' => '4'
            ]);
            Page::create([
                'name' => 'Integrations',
                'module_id' => '14',
                'sort' => '5'
            ]);

            Module::create([
                'id' => '15',
                'name' => 'Customers',
                'sort' => '15'
            ]);
            Module::create([
                'id' => '16',
                'name' => 'Players',
                'sort' => '16'
            ]);
            Page::create([
                'name' => 'Dashboard',
                'module_id' => '1',
                'sort' => '1'
            ]);
        }

        $clientPortal = Module::where('name', 'Client Portal')->first();

        if(!$clientPortal)
        {
            $client = Module::create([
                'name' => 'Client Portal',
                'sort' => '17'
            ]);

            $clientRequestCheck = Page::where('name', 'Client Requests')->first();

            if(!$clientRequestCheck)
            {
                Page::create([
                    'name' => 'Client Requests',
                    'module_id' => $client->id,
                    'sort' => '1'
                ]);
            }
        }

        $requestModule = Module::where('name', 'Request')->first();
        
        if($requestModule)
        {
            $requestView = Page::where('name', 'View Requests')->where('module_id', $requestModule->id)->first();

            if(!$requestView)
            {
                Page::create([
                    'name' => 'View Requests',
                    'module_id' => $requestModule->id,
                    'sort' => '1'
                ]);
            }
        }

        $requestModule = Module::find(14);
        
        if($requestModule)
        {
            $requestView = Page::where('name', 'Reports')->where('module_id', $requestModule->id)->first();

            if(!$requestView)
            {
                Page::create([
                    'name' => 'Reports',
                    'module_id' => $requestModule->id,
                    'sort' => '2'
                ]);
           
            }
        }
    }
}
