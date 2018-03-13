<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            // Price For Every km
            $table->float('price_of_km');
            $table->string('main_email');

            $table->string('PAYPAL_SANDBOX_CLIENT_ID')->nullable();
            $table->string('PAYPAL_SANDBOX_SECRET')->nullable();

            //Client Location Lat and Long
            $table->decimal('main_long', 10, 7)->default(31.4099411);
            $table->decimal('main_lat', 10, 7)->default(34.1085036);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
