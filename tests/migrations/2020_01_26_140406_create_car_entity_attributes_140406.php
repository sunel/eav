<?php

use Eav\Attribute;
use Eav\EntityAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarEntityAttributes140406 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        			
			Attribute::add([
				'attribute_code' => 'slug',
				'entity_code' => 'car',
				'backend_class' => '',
				'backend_type' => 'static',
				'backend_table' =>  '',
				'frontend_class' =>  '',
				'frontend_type' => 'text',
				'frontend_label' => 'Slug',
				'source_class' =>  '',
				'default_value' => '',
				'is_required' => 0,
				'is_filterable' => 0,
				'is_searchable' => 0,
				'required_validate_class' =>  ''	
			]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        			
			Attribute::remove([
				'attribute_code' => 'slug',
				'entity_code' => 'car',
			]);

    }
}
