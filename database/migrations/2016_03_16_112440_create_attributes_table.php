<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('attribute_id');
            $table->integer('entity_id')->unsigned();
            $table->string('attribute_code', 50);
            $table->string('backend_class')->nullable();
            $table->string('backend_type');
            $table->string('backend_table')->nullable();
            $table->string('frontend_class')->nullable();
            $table->string('frontend_type');
            $table->string('frontend_label')->nullable();
            $table->string('source_class')->nullable();
            $table->text('default_value');
            $table->smallInteger('is_filterable')->unsigned()->default(0);
            $table->smallInteger('is_searchable')->unsigned()->default(0);
            $table->smallInteger('is_required')->unsigned()->default(0);
            $table->string('required_validate_class')->nullable();
            
            
            $table->unique(['entity_id', 'attribute_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attributes');
    }
}
