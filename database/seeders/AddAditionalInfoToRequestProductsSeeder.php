<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\RequestProduct;

class AddAditionalInfoToRequestProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = RequestProduct::get();

        foreach($products as $p)
        {
            $product = Product::find($p->product_id);

            $p->update([
                'additional_information' => $product->additional_information
            ]);
        }
    }
}
