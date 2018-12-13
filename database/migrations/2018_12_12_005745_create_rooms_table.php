<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('property_id');
            $table->String('name');
            $table->unsignedInteger('default_rate')->default(0);
            $table->unsignedInteger('bedrooms');
            $table->unsignedInteger('max_guests');
            $table->boolean('tax_inclusive');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("property_id")->references("id")->on("properties");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
