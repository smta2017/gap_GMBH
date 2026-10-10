<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductService;
use App\Models\RequestProduct;
use App\Models\Product;

class RemoveSerivesAndProducts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = ProductService::get();

        foreach($services as $service)
        {
            $products = Product::where('service_id', $service->id)->pluck('id')->toArray();
            $rP = RequestProduct::whereIn('product_id', $products)->count();

            if($rP == 0)
            {
                Product::where('service_id', $service->id)->forceDelete();
                $service->forceDelete();
            }
        }

        $servicesData = ProductService::whereIn('ref_code', ['CFU01', 'HER01', 'KLX90', 'KLX01'])->get();

        foreach($servicesData as $service)
        {
            $products = Product::where('service_id', $service->id)->pluck('id')->toArray();
            $rP = RequestProduct::whereIn('product_id', $products)->count();

            if($rP == 0)
            {
                Product::where('service_id', $service->id)->forceDelete();
                $service->forceDelete();
            }
        }

    }
}
