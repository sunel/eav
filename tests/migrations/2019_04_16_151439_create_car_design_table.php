<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarDesignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_design', function (Blueprint $table) {
            $table->integer('car_id')->unsigned();
            $table->integer('design_id')->unsigned();

            $table->foreign('car_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');

            $table->foreign('design_id')
                  ->references('id')->on('design')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('car_design');
    }
}
