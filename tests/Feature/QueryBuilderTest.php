<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;
use Eav\Attribute;

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
        $eloquent = $this->car();

        $this->assertNotNull($eloquent->getKey());
    }

    /** @test */
    public function it_can_update_entity()
    {
        $eloquent = $this->car();

        $this->assertNotNull($eloquent->getKey());

        $eloquent->name = 'Not a Flamethrower';

        $eloquent->save();

        $db = Cars::select(['name'])->find($eloquent->getKey());

        $this->assertEquals($db->name, 'Not a Flamethrower');
    }

    /** @test */
    public function it_can_mass_update()
    {
        $eloquent = $this->car();

        Cars::create([
            'name' => 'Flamethrower',
            'sku'  => '1HJK92_2',
            'description' => 'Not a Flamethrower'
        ]);

        $this->assertNull($eloquent->search);

        $p = Cars::whereNullAttribute('search');

        $p->update(['search' => 1]);

        $db = Cars::select(['attr.*'])->find($eloquent->getKey());

        $this->assertNotNull($db->search);
    }

    /** @test */
    public function it_can_fetch_without_attributes()
    {
        $eloquent = $this->car();

        $cars = Cars::all();

        $p = $cars->first();

        $this->assertNull($p->name);

        $this->assertEquals($eloquent->getKey(), $p->getKey());
    }

    /** @test */
    public function it_can_fetch_with_attributes()
    {
        $eloquent = $this->car();

        $cars = Cars::all(['attr.*']);

        $p = $cars->first();

        $this->assertNotNull($p->name);

        $this->assertEquals($eloquent->sku, $p->sku);
    }

    /** @test */
    public function it_can_fetch_specific_attributes()
    {
        $eloquent = $this->car();

        $cars = Cars::all(['name','description']);

        $p = $cars->first();

        $this->assertNull($p->sku);
        $this->assertNull($p->getKey());

        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_fetch_with_entity_table()
    {
        $eloquent = $this->car();

        $cars = Cars::all(['*','name','description']);

        $p = $cars->first();

        $this->assertNull($p->sku);

        $this->assertNotNull($p->getKey());
        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_fetch_with_specific_entity_table()
    {
        $eloquent = $this->car();

        $cars = Cars::all(['id','name','description']);

        $p = $cars->first();

        $this->assertNull($p->sku);

        $this->assertNotNull($p->getKey());
        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_fetch_with_get()
    {
        $eloquent = $this->car();

        $cars = Cars::whereAttribute('sku', '1HJK92')
            ->get(['name']);

        $p = $cars->first();

        $this->assertNull($p->sku);
        $this->assertNull($p->getKey());

        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_fetch_with_select()
    {
        $eloquent = $this->car();

        $cars = Cars::whereAttribute('sku', '1HJK92')
            ->select(['attr.*'])
            ->get();

        $p = $cars->first();

        $this->assertNotNull($p->sku);
        $this->assertNotNull($p->getKey());
        $this->assertEquals($eloquent->name, $p->name);
    }

    /** @test */
    public function it_can_join_an_attribute()
    {
        $cars = Cars::select('*');

        $attribute = \Eav\Attribute::findByCode('sku', 'car');
        $attribute->setEntity($cars->baseEntity());
        $attribute->addAttributeJoin($cars->getQuery(), 'left');

        $this->assertNotNull($cars->getQuery()->joins);
    }


    /** @test */
    public function it_can_have_nested_query()
    {
        $eloquent = $this->car();
        
        $cars = Cars::select('*');

        $cars = Cars::whereAttribute('sku', '=', '1HJK92')
            ->whereAttribute(function ($query) {
                $query->whereAttribute('description', '=', 'Something');
                $query->orWhereAttribute('name', '=', 'Flamethrower');
            })->select(['attr.*'])
            ->get();

        $this->assertTrue($cars->isNotEmpty());
    }

    private function car()
    {
        return Cars::create([
            'name' => 'Flamethrower',
            'sku'  => '1HJK92',
            'description' => 'Not a Flamethrower'
        ]);
    }
}
