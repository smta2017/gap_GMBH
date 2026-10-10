<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestRedirectEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_redirect_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_redirect_id')->nullable();
            $table->string('email')->nullable();
            $table->integer('type')->nullable(); // 0 -> email, 1 -> CC, 2 -> BCC

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_redirect_emails');
    }
}
