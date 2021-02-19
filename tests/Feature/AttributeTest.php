<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;
use Eav\Attribute;
use Eav\AttributeSet;
use Eav\AttributeOption;

class AttributeTest extends TestCase
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

        $this->entity = factory(Entity::class)->create([
            'entity_code' => 'custom'
        ]);
    }

    /** @test */
    public function it_can_be_added()
    {
        $sku = $this->addSku();

        $this->assertTrue($sku->getKey() != null);
    }


    /** @test */
    public function it_can_be_found_by_code()
    {
        $sku = $this->addSku();
        $skuDB = Attribute::findByCode('sku', 'custom');

        $this->assertEquals($sku->getKey(), $skuDB->getKey());
        $this->assertEquals($sku->attributeId(), $skuDB->attributeId());
        $this->assertEquals($sku->code(), $skuDB->code());
        $this->assertEquals($sku->entity()->entity_code, $skuDB->entity()->entity_code);
    }


    /** @test */
    public function it_can_be_removed()
    {
        $sku = $this->addSku();

        Attribute::remove([
            'attribute_code' => 'sku',
            'entity_code' => 'custom',
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Attribute::findByCode('sku', 'custom');
    }

    /** @test */
    public function it_cannot_have_dupicate()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $sku = $this->addSku();

        $sku2 = $this->addSku();
    }

    /** @test */
    public function it_can_have_options()
    {
        $sku = $this->addSku();

        $optionsData = [
            's' => 'Small',
            'm' => 'Medium',
            'l' => 'Large',
            'xl' => 'Xtra Large',
        ];

        AttributeOption::add($sku, $optionsData);

        $this->assertEquals($sku->options(), $optionsData);
    }

    /** @test */
    public function it_can_have_options_through_source()
    {
        $sku = $this->addWithSource();

        $optionsData = [
           '1' => 'Yes',
           '0'  => 'No'
         ];

        $this->assertEquals($sku->options(), $optionsData);
    }

    /** @test */
    public function it_can_have_options_through_migration()
    {
        $sku = $this->addWithOption();

        $optionsData = [
           '1' => 'Yes',
           '0'  => 'No'
         ];

        $this->assertEquals($sku->options(), $optionsData);
    }

    /** @test */
    public function only_select_can_have_source()
    {
        $sku = $this->addSku();

        $search = $this->addWithSource();

        $this->assertFalse($sku->usesSource());
        $this->assertTrue($search->usesSource());
    }

    /** @test */
    public function it_can_be_static()
    {
        $sku = $this->addSku();

        $sku1 = $this->addSku([
            'attribute_code' => 'sku1',
            'backend_type' => '',
        ]);

        $sku2 = $this->addSku([
            'attribute_code' => 'sku2',
            'backend_type' => 'static',
        ]);

        $this->assertFalse($sku->isStatic());
        $this->assertTrue($sku1->isStatic());
        $this->assertTrue($sku2->isStatic());
    }

    /** @test */
    public function it_can_get_id_by_code()
    {
        $sku = $this->addSku();

        $attribute = new Attribute();

        $this->assertEquals(
            $attribute->getIdByCode('sku', $this->entity->getKey()),
            $sku->getKey()
        );
    }

    /** @test */
    public function it_must_have_a_table()
    {
        $sku = $this->addSku();

        $this->assertEquals(
            $this->entity->code().'_string',
            $sku->backendTable()
        );
    }

    /** @test */
    public function it_can_have_default_value()
    {
        $sku = $this->addSku();

        $upc = $this->addSku([
            'attribute_code' => 'upc',
            'default_value' => '0',
        ]);

        $this->assertEquals($sku->defaultValue(), '');

        $this->assertEquals($upc->defaultValue(), '0');
    }


    /** @test */
    public function it_can_get_supported_values()
    {
        $sku = $this->addSku();

        $this->assertEquals($sku->code(), 'sku');
        $this->assertEquals($sku->backendType(), 'string');
        $this->assertEquals($sku->frontendInput(), 'text');
        $this->assertEquals($sku->frontendLabel(), 'Sku');
    }

    /** @test */
    public function it_can_insert_data()
    {
        $sku = $this->addSku([
            'attribute_code' => 'upc',
            'entity_code' => 'car',
        ]);

        $value = 'HGKHDGEYTT'. rand();

        $eloquent = new class() extends \Eav\Model {
            const ENTITY  = 'car';
            protected $table = 'cars';
        };

        $eloquent->save();

        $sku->insertAttribute($value, $eloquent->getKey());

        $this->assertDatabaseHas($sku->backendTable(), [
            'entity_type_id' => $sku->entity()->getKey(),
            'attribute_id' => $sku->getKey(),
            'entity_id' => $eloquent->getKey(),
            'value' => $value
        ]);
    }

    /** @test */
    public function it_can_update_data()
    {
        $sku = $this->addSku([
            'attribute_code' => 'upc',
            'entity_code' => 'car',
        ]);

        $value = 'HGKHDGEYTT'. rand();

        $eloquent = new class() extends \Eav\Model {
            const ENTITY  = 'car';
            protected $table = 'cars';
        };

        $eloquent->save();

        $sku->insertAttribute($value, $eloquent->getKey());

        $this->assertDatabaseHas($sku->backendTable(), [
            'entity_type_id' => $sku->entity()->getKey(),
            'attribute_id' => $sku->getKey(),
            'entity_id' => $eloquent->getKey(),
            'value' => $value
        ]);

        $value = 'HGKHDGEYTT'. rand();

        $sku->updateAttribute($value, $eloquent->getKey());

        $this->assertDatabaseHas($sku->backendTable(), [
            'entity_type_id' => $sku->entity()->getKey(),
            'attribute_id' => $sku->getKey(),
            'entity_id' => $eloquent->getKey(),
            'value' => $value
        ]);
    }

    /** @test */
    public function it_can_fetch_data()
    {
        $sku = $this->addSku([
            'attribute_code' => 'upc',
            'entity_code' => 'car',
        ]);

        $value = 'HGKHDGEYTT'. rand();

        $eloquent = new class() extends \Eav\Model {
            const ENTITY  = 'car';
            protected $table = 'cars';
        };

        $eloquent->save();

        $sku->insertAttribute($value, $eloquent->getKey());

        $this->assertDatabaseHas($sku->backendTable(), [
            'entity_type_id' => $sku->entity()->getKey(),
            'attribute_id' => $sku->getKey(),
            'entity_id' => $eloquent->getKey(),
            'value' => $value
        ]);

        $valueDB = $sku->fetchAttributeValue($eloquent->getKey());

        $this->assertEquals($value, $valueDB);
    }

    private function addWithSource()
    {
        return Attribute::add([
            'attribute_code' => 'search',
            'entity_code' => 'custom',
            'backend_class' => null,
            'backend_type' => 'boolean',
            'backend_table' =>  null,
            'frontend_class' =>  null,
            'frontend_type' => 'select',
            'frontend_label' => ucwords(str_replace('_', ' ', 'search')),
            'source_class' =>  \Eav\Attribute\Source\Boolean::class,
            'default_value' => 0,
            'is_required' => 0,
            'required_validate_class' =>  null
        ]);
    }

    private function addWithOption()
    {
        return Attribute::add([
            'attribute_code' => 'search',
            'entity_code' => 'custom',
            'backend_class' => null,
            'backend_type' => 'boolean',
            'backend_table' =>  null,
            'frontend_class' =>  null,
            'frontend_type' => 'select',
            'frontend_label' => ucwords(str_replace('_', ' ', 'search')),
            'source_class' => null,
            'options' => [
               '1' => 'Yes',
               '0'  => 'No'
             ],
            'default_value' => 0,
            'is_required' => 0,
            'required_validate_class' =>  null
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Entity::clearStaticCache();
    }
}
