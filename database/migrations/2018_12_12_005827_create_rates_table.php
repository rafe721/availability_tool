<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('property_id');
            $table->unsignedInteger('room_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('rate');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("property_id")->references("id")->on("properties");
            $table->foreign("room_id")->references("id")->on("rooms");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rates');
    }
}
