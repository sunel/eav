<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;
use Eav\Attribute;
use Eav\AttributeSet;
use Eav\AttributeGroup;
use Eav\EntityAttribute;

class AttributeGroupTest extends TestCase
{
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

        $this->assertEquals($group->attributes->count(), 1);
    }
}
