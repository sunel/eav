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
     * @var Eav\Entity
     */
    protected $entity_flat;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = factory(Entity::class)->create([
            'entity_code' => 'custom'
        ]);

        $this->entity_flat = factory(Entity::class)->states('flat')->create([
            'entity_code' => 'custom_1'
        ]);
    }


    /** @test */
    public function it_can_be_found_by_code()
    {
        $entityDB = Entity::findByCode($this->entity->entity_code);
        $this->assertEquals($this->entity->entity_id, $entityDB->entity_id);
        $this->assertEquals($this->entity->entity_code, $entityDB->entity_code);
    }

    /** @test */
    public function it_can_be_found_by_id()
    {
        $entityDB = Entity::findById($this->entity->entity_id);
        $this->assertEquals($this->entity->entity_code, $entityDB->entity_code);
        $this->assertEquals($this->entity->entity_id, $entityDB->entity_id);
    }

    /** @test */
    public function it_can_detect_flat_table()
    {
        $this->assertEquals($this->entity->canUseFlat(), 0);
        $this->assertEquals($this->entity_flat->canUseFlat(), 1);
    }

    /** @test */
    public function it_can_detect_flat_table_name()
    {
        $eloquent = new class() extends \Eav\Model {
            const ENTITY  = 'custom';
            protected $table = 'custom_table';
        };
        
        $eloquent_1 = new class() extends \Eav\Model {
            const ENTITY  = 'custom_1';
            protected $table = 'custom_table';
        };

        $this->assertEquals($eloquent->getTable(), 'custom_table');
        $this->assertEquals($eloquent_1->getTable(), 'custom_table_flat');
    }

    /** @test */
    public function it_can_detect_key_name()
    {
        $eloquent = new class() extends \Eav\Model {
            const ENTITY  = 'custom';
            protected $primaryKey = 'custom_id';
        };

        $entity = $eloquent->baseEntity();

        $this->assertEquals($entity->entityKey(), 'custom_id');
        $this->assertEquals($eloquent->getKeyName(), $entity->entityKey());
    }

    /** @test */
    public function it_can_detect_entity_table_name()
    {
        $eloquent = new class() extends \Eav\Model {
            const ENTITY  = 'custom';
            protected $table = 'custom_table';
        };

        $entity = $eloquent->baseEntity();

        $this->assertEquals($entity->entityTableName(), $eloquent->getTable());
    }

    /** @test */
    public function it_can_have_many_attributes()
    {
        $entity = Entity::findByCode('car');

        $this->assertTrue($entity->attributes->isNotEmpty());
    }

    /** @test */
    public function it_can_have_many_attribute_set()
    {
        $entity = Entity::findByCode('car');

        $this->assertTrue($entity->attributeSet->isNotEmpty());
    }

    /** @test */
    public function it_must_have_default_attribute_set()
    {
        $set = factory(AttributeSet::class)->create([
            'entity_id' => $this->entity->entity_id,
        ]);

        $this->entity->default_attribute_set_id = $set->attribute_set_id;
        $this->entity->save();

        $this->assertEquals($this->entity->entity_id, $set->entity_id);
        $this->assertEquals($this->entity->defaultAttributeSet->attribute_set_id, $set->attribute_set_id);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Entity::clearStaticCache();
    }
}
