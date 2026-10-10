<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the existing unique constraint
        Schema::table('bewotec_davinci_services', function (Blueprint $table) {
            $table->dropUnique('service_unique');
        });

        // Add the unique constraint with the new column
        Schema::table('bewotec_davinci_services', function (Blueprint $table) {
            $table->unique(['booking_code', 'requirement', 'catalog_code', 'date_from', 'date_to', 'duration', 'package_service_id', 'package_order'], 'service_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the unique constraint
        Schema::table('bewotec_davinci_services', function (Blueprint $table) {
            $table->dropUnique('service_unique');
        });

        // Re-add the unique constraint without the package_order column
        Schema::table('bewotec_davinci_services', function (Blueprint $table) {
            $table->unique(['booking_code', 'requirement', 'catalog_code', 'date_from', 'date_to', 'duration', 'package_service_id'], 'service_unique');
        });
    }
};
