<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGolfCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golf_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->string('name');
            $table->string('ref_id')->nullable();
            $table->string('letter_code')->nullable();
            $table->unsignedBigInteger('golf_course_style_id')->nullable();
            $table->text('website_description')->nullable();     
            $table->text('internal_description')->nullable(); 
            $table->string('designer')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('direct_contract')->nullable()->default(0);
            $table->boolean('via_dmc')->nullable()->default(0);
            $table->boolean('via_hotel')->nullable()->default(0);
            $table->unsignedBigInteger('handler_type_id')->nullable();
            $table->unsignedBigInteger('handler_id')->nullable();

            $table->integer('length_men')->nullable()->default('5900');
            $table->integer('length_women')->nullable()->default('5900');
            $table->integer('par_men')->nullable()->default('71');
            $table->integer('par_women')->nullable()->default('71');

            $table->integer('holes')->nullable()->default('18');
            $table->integer('course_rating')->nullable();
            $table->integer('club_rating')->nullable();
            $table->integer('slope_from')->nullable();
            $table->integer('slope_to')->nullable();
            $table->boolean('academy')->nullable()->default(0);
            $table->boolean('pros')->nullable()->default(0);

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

            $table->integer('start_frequency')->nullable();
            $table->integer('start_gift')->nullable();

            $table->boolean('membership')->default(0)->nullable();
            $table->integer('hcp_men')->nullable()->default(28);
            $table->integer('hcp_women')->nullable()->default(35);

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('golf_course_style_id')->references('id')->on('golf_course_styles')->onUpdate('cascade')->onDelete('restrict');

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
        Schema::dropIfExists('golf_courses');
    }
}
