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
                'backend_class' => null,
                'backend_type' => 'string',
                'backend_table' =>  null,
                'frontend_class' =>  null,
                'frontend_type' => 'text',
                'frontend_label' => ucwords(str_replace('_', ' ', 'sku')),
                'source_class' =>  null,
                'default_value' => '',
                'is_required' => 0,
                'required_validate_class' =>  null
            ]);
            
        Attribute::add([
                'attribute_code' => 'name',
                'entity_code' => 'car',
                'backend_class' => null,
                'backend_type' => 'string',
                'backend_table' =>  null,
                'frontend_class' =>  null,
                'frontend_type' => 'text',
                'frontend_label' => ucwords(str_replace('_', ' ', 'name')),
                'source_class' =>  null,
                'default_value' => '',
                'is_required' => 0,
                'required_validate_class' =>  null
            ]);

        Attribute::add([
                'attribute_code' => 'age',
                'entity_code' => 'car',
                'backend_class' => null,
                'backend_type' => 'integer',
                'backend_table' =>  null,
                'frontend_class' =>  null,
                'frontend_type' => 'text',
                'frontend_label' => ucwords(str_replace('_', ' ', 'age')),
                'source_class' =>  null,
                'default_value' => '',
                'is_required' => 0,
                'required_validate_class' =>  null
            ]);
            
        Attribute::add([
                'attribute_code' => 'search',
                'entity_code' => 'car',
                'backend_class' => null,
                'backend_type' => 'boolean',
                'backend_table' =>  null,
                'frontend_class' =>  null,
                'frontend_type' => 'select',
                'frontend_label' => ucwords(str_replace('_', ' ', 'search')),
                'source_class' =>  \Eav\Attribute\Source\Boolean::class,
                'default_value' => 0,
                'is_filterable' => 1,
                'is_required' => 0,
                'required_validate_class' =>  null
            ]);
            
        Attribute::add([
                'attribute_code' => 'description',
                'entity_code' => 'car',
                'backend_class' => null,
                'backend_type' => 'text',
                'backend_table' =>  null,
                'frontend_class' =>  null,
                'frontend_type' => 'textarea',
                'frontend_label' => ucwords(str_replace('_', ' ', 'description')),
                'source_class' =>  null,
                'default_value' => '',
                'is_required' => 0,
                'required_validate_class' =>  null
            ]);

        Attribute::add([
                'attribute_code' => 'purchased_at',
                'entity_code' => 'car',
                'backend_class' => null,
                'backend_type' => 'timestamp',
                'backend_table' =>  null,
                'frontend_class' =>  null,
                'frontend_type' => 'text',
                'frontend_label' => ucwords(str_replace('_', ' ', 'purchased_at')),
                'source_class' =>  null,
                'default_value' => '',
                'is_filterable' => 1,
                'is_required' => 0,
                'required_validate_class' =>  null
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
