<?php

use Eav\Attribute;
use Eav\EntityAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarEntityAttributes071258 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        			
			Attribute::add([
				'attribute_code' => 'sku',
				'entity_code' => 'car',
				'backend_class' => NULL,
				'backend_type' => 'string',
				'backend_table' =>  NULL,
				'frontend_class' =>  NULL,
				'frontend_type' => 'text',
				'frontend_label' => ucwords(str_replace('_',' ','sku')),
				'source_class' =>  NULL,
				'default_value' => '',
				'is_required' => 0,
				'required_validate_class' =>  NULL	
			]);
			
			Attribute::add([
				'attribute_code' => 'name',
				'entity_code' => 'car',
				'backend_class' => NULL,
				'backend_type' => 'string',
				'backend_table' =>  NULL,
				'frontend_class' =>  NULL,
				'frontend_type' => 'text',
				'frontend_label' => ucwords(str_replace('_',' ','name')),
				'source_class' =>  NULL,
				'default_value' => '',
				'is_required' => 0,
				'required_validate_class' =>  NULL	
			]);

			Attribute::add([
				'attribute_code' => 'age',
				'entity_code' => 'car',
				'backend_class' => NULL,
				'backend_type' => 'integer',
				'backend_table' =>  NULL,
				'frontend_class' =>  NULL,
				'frontend_type' => 'text',
				'frontend_label' => ucwords(str_replace('_',' ','age')),
				'source_class' =>  NULL,
				'default_value' => '',
				'is_required' => 0,
				'required_validate_class' =>  NULL	
			]);
			
			Attribute::add([
				'attribute_code' => 'search',
				'entity_code' => 'car',
				'backend_class' => NULL,
				'backend_type' => 'boolean',
				'backend_table' =>  NULL,
				'frontend_class' =>  NULL,
				'frontend_type' => 'select',
				'frontend_label' => ucwords(str_replace('_',' ','search')),
				'source_class' =>  \Eav\Attribute\Source\Boolean::class,
				'default_value' => 0,
				'is_required' => 0,
				'required_validate_class' =>  NULL	
			]);
			
			Attribute::add([
				'attribute_code' => 'description',
				'entity_code' => 'car',
				'backend_class' => NULL,
				'backend_type' => 'text',
				'backend_table' =>  NULL,
				'frontend_class' =>  NULL,
				'frontend_type' => 'textarea',
				'frontend_label' => ucwords(str_replace('_',' ','description')),
				'source_class' =>  NULL,
				'default_value' => '',
				'is_required' => 0,
				'required_validate_class' =>  NULL	
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
				'attribute_code' => 'sku',
				'entity_code' => 'car',
			]);
			
			Attribute::remove([
				'attribute_code' => 'name',
				'entity_code' => 'car',
			]);

			Attribute::remove([
				'attribute_code' => 'age',
				'entity_code' => 'car',
			]);
			
			Attribute::remove([
				'attribute_code' => 'search',
				'entity_code' => 'car',
			]);
			
			Attribute::remove([
				'attribute_code' => 'description',
				'entity_code' => 'car',
			]);

    }
}
