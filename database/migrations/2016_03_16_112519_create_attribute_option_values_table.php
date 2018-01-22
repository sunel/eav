<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeOptionValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_option_values', function (Blueprint $table) {
            $table->increments('value_id');
            $table->integer('option_id')->unsigned();
            $table->string('value');
            
            $table->foreign('option_id')
                  ->references('option_id')->on('attribute_options')
                  ->onDelete('cascade');
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attribute_option_values');
    }
}
