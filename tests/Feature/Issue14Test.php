<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;

class Pcs extends \Eav\Model
{
    const ENTITY  = 'pc';
}

class Issue14Test extends TestCase
{
    /** @test */
    public function it_can_show_2_attribute_same_name_of_2_entity()
    {
        $this->product();

        $carSkuAttr = Entity::findByCode('car')->attributes()->where('attribute_code', 'sku')->get();
        $pcSkuAttr = Entity::findByCode('pc')->attributes()->where('attribute_code', 'sku')->get();

        $this->assertNotEquals($carSkuAttr->first()->getKey(), $pcSkuAttr->first()->getKey());

        $this->assertEquals(Cars::all(['sku'])->first()->sku, 'PDO1HJK92');
        $this->assertEquals(Pcs::all(['sku'])->first()->sku, 'PDOBEEAM112');
    }

    private function product()
    {
        Cars::create([
            'sku'  => 'PDO1HJK92'
        ]);

        Pcs::create([
            'sku'  => 'PDOBEEAM112'
        ]);
    }
}
