<?php

use Eav\Entity;
use Eav\Attribute;
use Eav\AttributeSet;
use Eav\AttributeGroup;
use Eav\EntityAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_string', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->string('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });

        Schema::create('car_integer', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->integer('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });

        Schema::create('car_boolean', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->boolean('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });

        Schema::create('car_text', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->text('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });

        Schema::create('car_timestamp', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->timestamp('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });

        // comment from here to run test faster

        Schema::create('car_bigInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->bigInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_binary', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->binary('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
        
                        
        Schema::create('car_char', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->char('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_date', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->date('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_dateTime', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->dateTime('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_dateTimeTz', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->dateTimeTz('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_decimal', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->decimal('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_double', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->double('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_float', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->float('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_geometry', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->geometry('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_geometryCollection', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->geometryCollection('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_ipAddress', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->ipAddress('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_lineString', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->lineString('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_longText', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->longText('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_macAddress', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->macAddress('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_mediumInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->mediumInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_mediumText', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->mediumText('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_multiLineString', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->multiLineString('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_multiPoint', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->multiPoint('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_multiPolygon', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->multiPolygon('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_point', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->point('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_polygon', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->polygon('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_smallInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->smallInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_time', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->time('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_timeTz', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->timeTz('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });

        Schema::create('car_timestampTz', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->timestampTz('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_tinyInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->tinyInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_unsignedBigInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedBigInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_unsignedInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_unsignedMediumInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedMediumInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_unsignedSmallInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedSmallInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_unsignedTinyInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedTinyInteger('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_uuid', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->uuid('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('car_year', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->year('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('cars')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });

        // comment till here     
        
        $entity = Entity::create([
            'entity_code' => 'car',
            'entity_class' => 'App\Cars',
            'entity_table' => 'cars',
        ]);
        
        
        $attributeSet = AttributeSet::create([
            'attribute_set_name' => 'Default',
            'entity_id' => $entity->entity_id,
        ]);
        
        $entity->default_attribute_set_id = $attributeSet->attribute_set_id;
        $entity->save();
        
        $attributeGroup = AttributeGroup::create([
            'attribute_set_id' => $attributeSet->attribute_set_id,
            'attribute_group_name' => 'General',
        ]);

        $this->addTimeStampAttributes();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->removeTimeStampAttributes();
        
        Schema::drop('car_boolean');
            
        Schema::drop('car_integer');

        Schema::drop('car_string');
            
        Schema::drop('car_text');
            
        Schema::drop('car_timestamp');
        
        // comment from here to run test faster

        Schema::drop('car_bigInteger');
            
        Schema::drop('car_binary');
            
        Schema::drop('car_char');
            
        Schema::drop('car_date');
            
        Schema::drop('car_dateTime');
            
        Schema::drop('car_dateTimeTz');
            
        Schema::drop('car_decimal');
            
        Schema::drop('car_double');
            
        Schema::drop('car_float');
            
        Schema::drop('car_geometry');
            
        Schema::drop('car_geometryCollection');
            
        Schema::drop('car_ipAddress');
            
        Schema::drop('car_lineString');
            
        Schema::drop('car_longText');
            
        Schema::drop('car_macAddress');
            
        Schema::drop('car_mediumInteger');
            
        Schema::drop('car_mediumText');
            
        Schema::drop('car_multiLineString');
            
        Schema::drop('car_multiPoint');
            
        Schema::drop('car_multiPolygon');
            
        Schema::drop('car_point');
            
        Schema::drop('car_polygon');
            
        Schema::drop('car_smallInteger');
            
        Schema::drop('car_time');
            
        Schema::drop('car_timeTz');
            
        Schema::drop('car_timestampTz');
            
        Schema::drop('car_tinyInteger');
            
        Schema::drop('car_unsignedBigInteger');
            
        Schema::drop('car_unsignedInteger');
            
        Schema::drop('car_unsignedMediumInteger');
            
        Schema::drop('car_unsignedSmallInteger');
            
        Schema::drop('car_unsignedTinyInteger');
            
        Schema::drop('car_uuid');
            
        Schema::drop('car_year');
        
        // Comment till here
        
        $entity = Entity::where('entity_code', '=', 'car');
        $attributeSet = AttributeSet::where('attribute_set_name', '=', 'Default')
                        ->where('entity_id', '=', $entity->first()->entity_id);
        $attributeGroup = AttributeGroup::where('attribute_set_id', '=', $attributeSet->first()->attribute_set_id)
                        ->where('attribute_group_name', '=', 'General');
        
        
        $attributeGroup->delete();
        $attributeSet->delete();
        $entity->delete();
    }


    protected function addTimeStampAttributes()
    {
        Attribute::add([
            'attribute_code' => 'created_at',
            'entity_code' => 'car',
            'backend_class' => null,
            'backend_type' => 'static',
            'backend_table' =>  null,
            'frontend_class' =>  null,
            'frontend_type' => 'input',
            'frontend_label' => ucwords(str_replace('_', ' ', 'created_at')),
            'source_class' =>  null,
            'default_value' => '',
            'is_required' => 0,
            'required_validate_class' =>  null
        ]);

        EntityAttribute::map([
            'attribute_code' => 'created_at',
            'entity_code' => 'car',
            'attribute_set' => 'Default',
            'attribute_group' => 'General'
        ]);

        Attribute::add([
            'attribute_code' => 'updated_at',
            'entity_code' => 'car',
            'backend_class' => null,
            'backend_type' => 'static',
            'backend_table' =>  null,
            'frontend_class' =>  null,
            'frontend_type' => 'input',
            'frontend_label' => ucwords(str_replace('_', ' ', 'updated_at')),
            'source_class' =>  null,
            'default_value' => '',
            'is_required' => 0,
            'required_validate_class' =>  null
        ]);

        EntityAttribute::map([
            'attribute_code' => 'updated_at',
            'entity_code' => 'car',
            'attribute_set' => 'Default',
            'attribute_group' => 'General'
        ]);
    }

    protected function removeTimeStampAttributes()
    {
        EntityAttribute::unmap([
            'attribute_code' => 'created_at',
            'entity_code' => 'car',
        ]);

        Attribute::remove([
            'attribute_code' => 'created_at',
            'entity_code' => 'car',
        ]);

        EntityAttribute::unmap([
            'attribute_code' => 'updated_at',
            'entity_code' => 'car',
        ]);

        Attribute::remove([
            'attribute_code' => 'updated_at',
            'entity_code' => 'car',
        ]);
    }
}
