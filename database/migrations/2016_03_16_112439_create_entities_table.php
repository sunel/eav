<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('entity_id');
            $table->string('entity_code', 50)->unique();
            $table->string('entity_class');
            $table->string('entity_table');
            $table->integer('default_attribute_set_id')->unsigned()->nullable();
            $table->string('additional_attribute_table')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('entities');
    }
}
