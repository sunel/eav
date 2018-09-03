<?php

namespace Eav\TestCase\Feature;

class FacetTest extends TestCase
{
    /** @test */
    public function it_can_generate_facet()
    {
        $this->product();

        $search = Cars::whereAttribute('sku', 'like', 'PDO%');

        $fa = $search->getFacets();

        $this->assertTrue($fa->isNotEmpty());
    }


    /** @test */
    public function it_can_generate_facet_with_count()
    {
        $this->product();

        $search = Cars::whereAttribute('sku', 'like', 'PDO%');

        $fa = $search->getFacets(true);

        $item = $fa->first()->first();

        $this->assertTrue(isset($item['count']));
    }


    private function product()
    {
        Cars::create([
            'name' => 'Flamethrower',
            'sku'  => 'PDO1HJK92',
            'age' => rand(50, 100),
            'search' => 1,
            'purchased_at' => new \DateTime('2018-09-02T15:02:01.012345Z')
        ]);

        Cars::create([
            'name' => 'Space Beem',
            'sku'  => 'PDOBEEAM112',
            'description' => 'Definitely Not a Flamethrower',
            'age' => 14,
            'search' => 0,
            'purchased_at' => new \DateTime('2018-08-21T15:03:01.012345Z')
        ]);
    }
}
