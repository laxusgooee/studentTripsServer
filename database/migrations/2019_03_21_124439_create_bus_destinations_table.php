<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bus_destinations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('bus_id');
            $table->bigInteger('terminal_from');
            $table->bigInteger('terminal_to');
            $table->decimal('price')->default('0');
            $table->dateTime('departing_date');
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
        Schema::dropIfExists('bus_destinations');
    }
}
