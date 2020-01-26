<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;

class Tesla2 extends \Eav\Model
{
    const ENTITY  = 'car';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "cars";

    public static function boot()
    {
        parent::boot();

        static::creating(function($data) {
            $data->slug = 'test';
        });
    }
    
}

class Issue40Test extends TestCase
{
    /** @test */
    public function model_events_should_work()
    {
        $car = Tesla2::create([
            'name' => 'Flamethrower',
            'sku'  => 'PDO1HJK92',
            'age' => rand(50, 100),
            'search' => 1
        ]);

        $this->assertNotNull($car->slug);

        $this->assertEquals($car->slug, 'test');

        $db = Cars::select(['attr.*'])->find($car->getKey());

        $this->assertNotNull($db->slug);

        $this->assertEquals($db->slug, 'test');
    }
}
