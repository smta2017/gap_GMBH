<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotConfirmMailToRequestProductTeeTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_product_tee_times', function (Blueprint $table) {
            $table->timestamp('not_confirm_mail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_product_tee_times', function (Blueprint $table) {
            $table->dropColumn('not_confirm_mail');
        });
    }
}
