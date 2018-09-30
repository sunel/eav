<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;
use Eav\Attribute;
use Eav\AttributeSet;
use Eav\AttributeGroup;
use Eav\EntityAttribute;

class AttributeSetTest extends TestCase
{
    /** @test */
    public function it_must_be_associated_with_entity()
    {
        $entity = factory(Entity::class)->create([
            'entity_code' => 'custom'
        ]);

        factory(AttributeSet::class, 5)->create([
            'entity_id' => $entity->entity_id,
        ]);

        $this->assertEquals($entity->attributeSet->count(), 5);
    }

    /** @test */
    public function it_can_have_multiple_group()
    {
        $entity = factory(Entity::class)->create([
            'entity_code' => 'custom'
        ]);

        $set = factory(AttributeSet::class)->create([
            'entity_id' => $entity->entity_id,
        ]);

        factory(AttributeGroup::class, 5)->create([
            'attribute_set_id' => $set->getKey(),
        ]);

        $this->assertEquals($set->attributeGroup->count(), 5);
    }


    /** @test */
    public function it_must_retrive_associated_attributes()
    {
        $entity = factory(Entity::class)->create([
            'entity_code' => 'custom'
        ]);

        $set = factory(AttributeSet::class)->create([
            'entity_id' => $entity->entity_id,
        ]);

        $group = factory(AttributeGroup::class)->create([
            'attribute_set_id' => $set->getKey(),
        ]);

        $sku = $this->addSku();

        EntityAttribute::map([
            'attribute_code' => $sku->code(),
            'entity_code' => $entity->code(),
            'attribute_set' => $set->name(),
            'attribute_group' => $group->name()
        ]);

        $this->assertEquals($set->attributes->count(), 1);
    }
}
