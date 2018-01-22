<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->increments('attribute_group_id');
            $table->integer('attribute_set_id')->unsigned();
            $table->string('attribute_group_name');
            
            $table->foreign('attribute_set_id')
                  ->references('attribute_set_id')->on('attribute_sets')
                  ->onDelete('cascade');
                  
            $table->unique(['attribute_set_id', 'attribute_group_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attribute_groups');
    }
}
