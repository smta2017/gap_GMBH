<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->string('ref_id')->nullable();
            $table->string('letter_code')->nullable();
            $table->integer('number_of_rooms')->nullable();
            $table->text('website_description')->nullable();     
            $table->text('internal_description')->nullable(); 
            
            $table->boolean('active')->default(1);
            $table->boolean('direct_contract')->default(1);
            $table->boolean('via_dmc')->default(1);
            $table->unsignedBigInteger('handler_type_id')->nullable();
            $table->unsignedBigInteger('handler_id')->nullable();

            $table->boolean('is_company_address')->nullable()->default(0);
            
            $table->string('delegate_name')->nullable();
            $table->string('delegate_email')->nullable();
            $table->string('delegate_mobile_number')->nullable();
            $table->string('delegate_user_id')->nullable();
            $table->string('assigned_user_id')->nullable();

            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('street')->nullable();
            $table->string('postal_code')->nullable();
            $table->longText('location_link')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();

            $table->boolean('payee')->default(1)->nullable();
            $table->boolean('is_payee_only')->default(1)->nullable();
            $table->boolean('payee_key_created')->default(1)->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_location')->nullable();
            $table->string('account_number')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();

            $table->string('reservation_email')->nullable();
            $table->string('booking_accounting_id')->nullable();

            $table->boolean('has_golf_course')->default(1)->nullable();
            $table->boolean('golf_desk')->default(1)->nullable();
            $table->boolean('golf_shuttle')->default(1)->nullable();
            $table->string('storage_room')->nullable();

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('handler_type_id')->references('id')->on('company_types')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('handler_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotels');
    }
}
