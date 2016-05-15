<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_attributes', function (Blueprint $table) {
            $table->integer('attribute_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->integer('attribute_set_id')->unsigned();
            $table->integer('attribute_group_id')->unsigned();
            
            $table->foreign('attribute_id')
                  ->references('attribute_id')->on('attributes')
                  ->onDelete('cascade');
            
            $table->foreign('attribute_set_id')
                  ->references('attribute_set_id')->on('attribute_sets')
                  ->onDelete('cascade');
                  
            $table->foreign('attribute_group_id')
                  ->references('attribute_group_id')->on('attribute_groups')
                  ->onDelete('cascade');
                  
            $table->foreign('entity_id')
                  ->references('entity_id')->on('entities')
                  ->onDelete('cascade');
                  
            $table->unique(['attribute_set_id', 'attribute_id']);
            $table->index('attribute_set_id');
            $table->index('attribute_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('entity_attributes');
    }
}
