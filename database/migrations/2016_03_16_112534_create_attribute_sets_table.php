<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_sets', function (Blueprint $table) {
            $table->increments('attribute_set_id');
            $table->integer('entity_id')->unsigned();
            $table->string('attribute_set_name');
            
            $table->foreign('entity_id')
                  ->references('entity_id')->on('entities')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id', 'attribute_set_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attribute_sets');
    }
}
