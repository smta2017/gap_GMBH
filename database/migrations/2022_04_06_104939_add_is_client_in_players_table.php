<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsClientInPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_players', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_company_id')->nullable()->after('hcp');
            $table->unsignedBigInteger('leader_type_id')->nullable()->after('hcp');

            $table->boolean('is_leader')->nullable()->default(0)->after('hcp');
            $table->boolean('is_client')->nullable()->default(0)->after('hcp');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_players', function (Blueprint $table) {
            //
        });
    }
}
