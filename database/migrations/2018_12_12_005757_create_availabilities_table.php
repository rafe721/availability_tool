<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availabilities', function (Blueprint $table) {
//            $table->increments('id'); Feel this table need not have a primary key
            // Eloquent does not support composite keys...
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('room_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('available');

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
        Schema::dropIfExists('availabilities');
    }
}
