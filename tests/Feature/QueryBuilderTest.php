<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;
use Eav\Attribute;
use Eav\AttributeSet;
use Eav\AttributeGroup;
use Eav\EntityAttribute;

class Products extends \Eav\Model {
    const ENTITY  = 'product';
}

class QueryBuilderTest extends TestCase
{

	/** @test */
    public function it_must_entity_assigned()
    {
    	$this->expectException(\Exception::class);

    	$eloquent = new class() extends \Eav\Model {
        };
    }

	/** @test */
    public function it_can_create_entity()
    {
        $eloquent = $this->product();

		$this->assertNotNull($eloquent->getKey());
    }

    /** @test */
    public function it_can_update_entity()
    {
        $eloquent = $this->product();

		$this->assertNotNull($eloquent->getKey());

		$eloquent->name = 'Not a Flamethrower';

		$eloquent->save();

		$db = Products::select(['name'])->find($eloquent->getKey());

		$this->assertEquals($db->name, 'Not a Flamethrower');
    }

    /** @test */
    public function it_can_mass_update()
    {
        $eloquent = $this->product();

		Products::create([
		    'name' => 'Flamethrower',
		    'sku'  => '1HJK92_2',
		    'description' => 'Not a Flamethrower'
		]);

		$this->assertNull($eloquent->search);

		$p = Products::whereNullAttribute('search');

		$p->update(['search' => 1]);

		$db = Products::select(['attr.*'])->find($eloquent->getKey());

		$this->assertNotNull($db->search);
    }

    /** @test */
    public function it_can_fetch_without_attributes()
    {
        $eloquent = $this->product();

        $products = Products::all();

        $p = $products->first();

        $this->assertNull($p->name);

        $this->assertEquals($eloquent->getKey(), $p->getKey());
    }

    /** @test */
    public function it_can_fetch_with_attributes()
    {
        $eloquent = $this->product();

        $products = Products::all(['attr.*']);

        $p = $products->first();

        $this->assertNotNull($p->name);

        $this->assertEquals($eloquent->sku, $p->sku);
    }

    /** @test */
    public function it_can_fetch_specific_attributes()
    {
        $eloquent = $this->product();

        $products = Products::all(['name','description']);

        $p = $products->first();

        $this->assertNull($p->sku);
        $this->assertNull($p->getKey());

        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_fetch_with_entity_table()
    {
        $eloquent = $this->product();

        $products = Products::all(['*','name','description']);

        $p = $products->first();

        $this->assertNull($p->sku);

        $this->assertNotNull($p->getKey());
        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_fetch_with_specific_entity_table()
    {
        $eloquent = $this->product();

        $products = Products::all(['id','name','description']);

        $p = $products->first();

        $this->assertNull($p->sku);

        $this->assertNotNull($p->getKey());
        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_fetch_with_get()
    {
        $eloquent = $this->product();

        $products = Products::whereAttribute('sku', '1HJK92')
			->get(['name']);

        $p = $products->first();

        $this->assertNull($p->sku);
        $this->assertNull($p->getKey());

        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_fetch_with_select()
    {
        $eloquent = $this->product();

        $products = Products::whereAttribute('sku', '1HJK92')
			->select(['attr.*'])
			->get();

        $p = $products->first();

        $this->assertNotNull($p->sku);
        $this->assertNotNull($p->getKey());
        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_join_an_attribute()
    {
    	$products = Products::select('*');

	    $attribute = \Eav\Attribute::findByCode('sku', 'product');
	    $attribute->setEntity($products->baseEntity());
	    $attribute->addAttributeJoin($products->getQuery(), 'left');

	    $this->assertNotNull($products->getQuery()->joins);
    }

    private function product()
    {
    	return Products::create([
		    'name' => 'Flamethrower',
		    'sku'  => '1HJK92',
		    'description' => 'Not a Flamethrower'
		]);
    }
}
