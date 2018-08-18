<?php

use Eav\Entity;
use Eav\Attribute;
use Eav\AttributeSet;
use Eav\AttributeGroup;
use Eav\EntityAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    				
		Schema::create('product_bigInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->bigInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_binary', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->binary('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_boolean', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->boolean('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_char', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->char('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_date', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->date('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_dateTime', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->dateTime('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_dateTimeTz', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->dateTimeTz('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_decimal', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->decimal('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_double', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->double('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_float', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->float('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_geometry', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->geometry('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_geometryCollection', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->geometryCollection('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_integer', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->integer('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_ipAddress', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->ipAddress('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_lineString', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->lineString('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_longText', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->longText('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_macAddress', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->macAddress('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_mediumInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->mediumInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_mediumText', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->mediumText('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_multiLineString', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->multiLineString('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_multiPoint', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->multiPoint('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_multiPolygon', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->multiPolygon('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_point', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->point('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_polygon', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->polygon('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_smallInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->smallInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_string', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->string('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_text', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->text('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_time', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->time('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_timeTz', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->timeTz('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_timestamp', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->timestamp('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_timestampTz', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->timestampTz('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_tinyInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->tinyInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_unsignedBigInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedBigInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_unsignedInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_unsignedMediumInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedMediumInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_unsignedSmallInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedSmallInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_unsignedTinyInteger', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->unsignedTinyInteger('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_uuid', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->uuid('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        			
		Schema::create('product_year', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->year('value')->default(NULL)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
            	  ->references('id')->on('products')
				  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
			$table->index('attribute_id');
			$table->index('entity_id');        	  
        });
	        
        
        $entity = Entity::create([
        	'entity_code' => 'product',
        	'entity_class' => 'App\Product',
        	'entity_table' => 'products',
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
        
    				
		Schema::drop('product_bigInteger');
			
		Schema::drop('product_binary');
			
		Schema::drop('product_boolean');
			
		Schema::drop('product_char');
			
		Schema::drop('product_date');
			
		Schema::drop('product_dateTime');
			
		Schema::drop('product_dateTimeTz');
			
		Schema::drop('product_decimal');
			
		Schema::drop('product_double');
			
		Schema::drop('product_float');
			
		Schema::drop('product_geometry');
			
		Schema::drop('product_geometryCollection');
			
		Schema::drop('product_integer');
			
		Schema::drop('product_ipAddress');			
			
		Schema::drop('product_lineString');
			
		Schema::drop('product_longText');
			
		Schema::drop('product_macAddress');
			
		Schema::drop('product_mediumInteger');
			
		Schema::drop('product_mediumText');
			
		Schema::drop('product_multiLineString');
			
		Schema::drop('product_multiPoint');
			
		Schema::drop('product_multiPolygon');
			
		Schema::drop('product_point');
			
		Schema::drop('product_polygon');
			
		Schema::drop('product_smallInteger');
			
		Schema::drop('product_string');
			
		Schema::drop('product_text');
			
		Schema::drop('product_time');
			
		Schema::drop('product_timeTz');
			
		Schema::drop('product_timestamp');
			
		Schema::drop('product_timestampTz');
			
		Schema::drop('product_tinyInteger');
			
		Schema::drop('product_unsignedBigInteger');			
			
		Schema::drop('product_unsignedInteger');
			
		Schema::drop('product_unsignedMediumInteger');
			
		Schema::drop('product_unsignedSmallInteger');
			
		Schema::drop('product_unsignedTinyInteger');
			
		Schema::drop('product_uuid');
			
		Schema::drop('product_year');
        
        
        $entity = Entity::where('entity_code', '=', 'product');               
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
            'entity_code' => 'product',
            'backend_class' => NULL,
            'backend_type' => 'static',
            'backend_table' =>  NULL,
            'frontend_class' =>  NULL,
            'frontend_type' => 'input',
            'frontend_label' => ucwords(str_replace('_',' ','created_at')),
            'source_class' =>  NULL,
            'default_value' => '',
            'is_required' => 0,
            'required_validate_class' =>  NULL  
        ]);

        EntityAttribute::map([
            'attribute_code' => 'created_at',
            'entity_code' => 'product',
            'attribute_set' => 'Default',
            'attribute_group' => 'General'
        ]);

        Attribute::add([
            'attribute_code' => 'updated_at',
            'entity_code' => 'product',
            'backend_class' => NULL,
            'backend_type' => 'static',
            'backend_table' =>  NULL,
            'frontend_class' =>  NULL,
            'frontend_type' => 'input',
            'frontend_label' => ucwords(str_replace('_',' ','updated_at')),
            'source_class' =>  NULL,
            'default_value' => '',
            'is_required' => 0,
            'required_validate_class' =>  NULL  
        ]);

        EntityAttribute::map([
            'attribute_code' => 'updated_at',
            'entity_code' => 'product',
            'attribute_set' => 'Default',
            'attribute_group' => 'General'
        ]);


    }

    protected function removeTimeStampAttributes()
    {
        EntityAttribute::unmap([
            'attribute_code' => 'created_at',
            'entity_code' => 'product',
        ]);

        Attribute::remove([
            'attribute_code' => 'created_at',
            'entity_code' => 'product',
        ]);

        EntityAttribute::unmap([
            'attribute_code' => 'updated_at',
            'entity_code' => 'product',
        ]);

        Attribute::remove([
            'attribute_code' => 'updated_at',
            'entity_code' => 'product',
        ]);
    }
}
