<?php

use Eav\Attribute;
use Eav\EntityAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarEntityAttributesMap140406 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        			
			EntityAttribute::map([
				'attribute_code' => 'slug',
				'entity_code' => 'car',
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
				'attribute_code' => 'slug',
				'entity_code' => 'car',
			]);

    }
}
