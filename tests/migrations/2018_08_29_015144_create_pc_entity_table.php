<?php

use Eav\Entity;
use Eav\Attribute;
use Eav\AttributeSet;
use Eav\AttributeGroup;
use Eav\EntityAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePcEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pc_boolean', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->boolean('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('pcs')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('pc_date', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->date('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('pcs')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('pc_dateTime', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->dateTime('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('pcs')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('pc_double', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->double('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('pcs')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('pc_integer', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->integer('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('pcs')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('pc_text', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->text('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('pcs')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
                        
        Schema::create('pc_string', function (Blueprint $table) {
            $table->increments('value_id')->comment('Value ID');
            $table->smallInteger('entity_type_id')->unsigned()->default(0)->comment('Entity Type ID');
            $table->integer('attribute_id')->unsigned()->default(0)->comment('Attribute ID');
            $table->integer('entity_id')->unsigned()->default(0)->comment('Entity ID');
            
            $table->string('value')->default(null)->nullable()->comment('Value');
            
            $table->foreign('entity_id')
                  ->references('id')->on('pcs')
                  ->onDelete('cascade');
            
            $table->unique(['entity_id','attribute_id']);
            $table->index('attribute_id');
            $table->index('entity_id');
        });
            
        
        $entity = Entity::create([
            'entity_code' => 'pc',
            'entity_class' => '\App\Pcs',
            'entity_table' => 'pcs',
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
        
                    
        Schema::drop('pc_boolean');
            
        Schema::drop('pc_date');
            
        Schema::drop('pc_dateTime');
            
        Schema::drop('pc_double');
            
        Schema::drop('pc_integer');
            
        Schema::drop('pc_text');
            
        Schema::drop('pc_string');
        
        
        $entity = Entity::where('entity_code', '=', 'pc');
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
            'entity_code' => 'pc',
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
            'entity_code' => 'pc',
            'attribute_set' => 'Default',
            'attribute_group' => 'General'
        ]);

        Attribute::add([
            'attribute_code' => 'updated_at',
            'entity_code' => 'pc',
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
            'entity_code' => 'pc',
            'attribute_set' => 'Default',
            'attribute_group' => 'General'
        ]);
    }

    protected function removeTimeStampAttributes()
    {
        EntityAttribute::unmap([
            'attribute_code' => 'created_at',
            'entity_code' => 'pc',
        ]);

        Attribute::remove([
            'attribute_code' => 'created_at',
            'entity_code' => 'pc',
        ]);

        EntityAttribute::unmap([
            'attribute_code' => 'updated_at',
            'entity_code' => 'pc',
        ]);

        Attribute::remove([
            'attribute_code' => 'updated_at',
            'entity_code' => 'pc',
        ]);
    }
}
