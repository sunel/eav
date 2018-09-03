<?php

use Eav\Attribute;
use Eav\EntityAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePcEntityAttributesMap015205 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        EntityAttribute::map([
                'attribute_code' => 'sku',
                'entity_code' => 'pc',
                'attribute_set' => 'Default',
                'attribute_group' => 'General'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        EntityAttribute::unmap([
                'attribute_code' => 'sku',
                'entity_code' => 'pc',
            ]);
    }
}
