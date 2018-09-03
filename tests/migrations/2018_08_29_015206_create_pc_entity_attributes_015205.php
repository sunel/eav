<?php

use Eav\Attribute;
use Eav\EntityAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePcEntityAttributes015205 extends Migration
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
                'entity_code' => 'pc',
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
                'entity_code' => 'pc',
            ]);
    }
}
