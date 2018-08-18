<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;
use Eav\AttributeSet;

class EntityTest extends TestCase
{
	/**
	 * @var Eav\Entity
	 */
	protected $entity;

	/**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = factory(Entity::class)->create();
        $set = factory(AttributeSet::class)->create([
        	'entity_id' => $this->entity->entity_id,
        ]);

        $this->entity->default_attribute_set_id = $set->attribute_set_id;        
        $this->entity->save();
    }


    /** @test */
    public function it_creates_entity()
    {
        $entityDB = \DB::table('entities')->where('entity_id', '=', 1)->first();
        $this->assertEquals($this->entity->entity_code, $entityDB->entity_code);       
    }


    /** @test */
    public function it_can_be_found_by_code()
    {    
     	$entity = factory(Entity::class, 2)->create();     	
        $entityDB = Entity::findByCode($entity->last()->entity_code);
        $this->assertEquals($entity->last()->entity_id, $entityDB->entity_id);
        $this->assertEquals($entity->last()->entity_code, $entityDB->entity_code);       
    }

    /** @test */
    public function it_can_be_found_by_id()
    {    
     	$entity = factory(Entity::class, 3)->create();     	
        $entityDB = Entity::findById($entity->last()->entity_id);
        $this->assertEquals($entity->last()->entity_code, $entityDB->entity_code);
        $this->assertEquals($entity->last()->entity_id, $entityDB->entity_id);    
    }

    /** @test */
    public function it_can_detect_flat_table()
    {    
     	$entity = factory(Entity::class)->create();     	        
        $this->assertEquals($entity->canUseFlat(), 0);

        $entity = factory(Entity::class)->states('flat')->create();     	        
        $this->assertEquals($entity->canUseFlat(), 1);   
    }
}