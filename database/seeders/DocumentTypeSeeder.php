<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(DocumentType::count() == 0)
        {
            DocumentType::create([
                'name' => 'CR',
                'status' => '1',
            ]);
            DocumentType::create([
                'name' => 'Contract',
                'status' => '1',
            ]);
            DocumentType::create([
                'name' => 'TAX Card',
                'status' => '1',
            ]);
            DocumentType::create([
                'name' => 'Certificat',
                'status' => '1',
            ]);
        }
    }
}
