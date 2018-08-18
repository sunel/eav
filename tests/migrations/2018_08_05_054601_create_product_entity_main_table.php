<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductEntityMainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('entity_id')->unsigned();
			$table->integer('attribute_set_id')->unsigned();
			
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
    	Schema::drop('products');       
    }
}
