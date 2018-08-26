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
        $this->assertEquals($sku->getAttributeId(), $skuDB->getAttributeId());
        $this->assertEquals($sku->getAttributeCode(), $skuDB->getAttributeCode());
        $this->assertEquals($sku->getEntity()->entity_code, $skuDB->getEntity()->entity_code);       
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
    		$this->entity->getEntityTableName().'_string',
    		$sku->getBackendTable()
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

    	$this->assertEquals($sku->getDefaultValue(), '');

    	$this->assertEquals($upc->getDefaultValue(), '0');
    }


    /** @test */
    public function it_can_get_supported_values()
    {
    	$sku = $this->addSku();

    	$this->assertEquals($sku->getAttributeCode(), 'sku');
    	$this->assertEquals($sku->getBackendType(), 'string');
    	$this->assertEquals($sku->getFrontendInput(), 'text');
    	$this->assertEquals($sku->getFrontendLabel(), 'Sku');
    }

    private function addSku($override = null)
    {
    	$data = [
			'attribute_code' => 'sku',
			'entity_code' => 'custom',
			'backend_class' => NULL,
			'backend_type' => 'string',
			'backend_table' =>  NULL,
			'frontend_class' =>  NULL,
			'frontend_type' => 'text',
			'frontend_label' => ucwords(str_replace('_',' ','sku')),
			'source_class' =>  NULL,
			'default_value' => '',
			'is_required' => 0,
			'required_validate_class' =>  NULL	
		];

		if($override) {
			$data = array_merge($data, $override);
		}

    	return Attribute::add($data);
    }


    private function addWithSource()
    {
    	return Attribute::add([
			'attribute_code' => 'search',
			'entity_code' => 'custom',
			'backend_class' => NULL,
			'backend_type' => 'boolean',
			'backend_table' =>  NULL,
			'frontend_class' =>  NULL,
			'frontend_type' => 'select',
			'frontend_label' => ucwords(str_replace('_',' ','search')),
			'source_class' =>  \Eav\Attribute\Source\Boolean::class,
			'default_value' => 0,
			'is_required' => 0,
			'required_validate_class' =>  NULL	
		]);
    }

    private function addWithOption()
    {
    	return Attribute::add([
			'attribute_code' => 'search',
			'entity_code' => 'custom',
			'backend_class' => NULL,
			'backend_type' => 'boolean',
			'backend_table' =>  NULL,
			'frontend_class' =>  NULL,
			'frontend_type' => 'select',
			'frontend_label' => ucwords(str_replace('_',' ','search')),
			'source_class' => null,
		    'options' => [
		       '1' => 'Yes',
		       '0'  => 'No'
		     ],
			'default_value' => 0,
			'is_required' => 0,
			'required_validate_class' =>  NULL	
		]);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Entity::clearStaticCache();
    }
}