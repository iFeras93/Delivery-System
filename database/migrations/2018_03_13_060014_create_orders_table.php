<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code');

            //Client Location Lat and Long
            $table->decimal('client_long', 10, 7);
            $table->decimal('client_lat', 10, 7);

            /**
             * Order Status
             * 0= pending
             * 1= in progress
             * 2= delivered
             * 3= canceled
             */
            $table->tinyInteger('status')->default(0);

            //Total Price For Product
            $table->float('product_price');

            //Total Price For With  distance
            $table->float('total_price');


            // This For Get User Make An Order
            $table->integer('client_id')->unsigned();

            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
