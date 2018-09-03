<?php

namespace Eav\TestCase\Feature;

use Eav\Entity;

class Tesla extends \Eav\Model
{
    const ENTITY  = 'car';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "cars";

    public function designs()
    {
        return $this->belongsToMany(Designs::class, 'car_design', 'car_id', 'design_id')->withTimestamps();
    }
}

class Designs extends \Illuminate\Database\Eloquent\Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "design";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public function cars()
    {
        return $this->belongsToMany(Tesla::class, 'car_design', 'design_id', 'car_id')->withTimestamps();
    }
}

class Issue10Test extends TestCase
{
    /** @test */
    public function entity_can_have_relationship_attached()
    {
        $car = $this->product();


        $cars = Tesla::select(['attr.*'])->find($car->getKey());
        $designs = $cars->designs;

        $this->assertEquals($designs->count(), 2);
        $this->assertEquals($designs->first()->name, 'Model3');
    }

    /** @test */
    public function entity_can_be_loaed_by_relationship()
    {
        $car = $this->product();

        $designs = Designs::findOrFail(1);
        $cars = $designs->cars()->select(['attr.*'])->get();

        $this->assertEquals($cars->count(), 1);
        $this->assertEquals($cars->first()->sku, 'PDO1HJK92');
    }

    private function product()
    {
        $car = Tesla::create([
            'name' => 'Flamethrower',
            'sku'  => 'PDO1HJK92',
            'age' => rand(50, 100),
            'search' => 1
        ]);

        $car->designs()->saveMany([
            new Designs(['name' => 'Model3']),
            new Designs(['name' => 'Model4']),
        ]);

        return $car;
    }
}
