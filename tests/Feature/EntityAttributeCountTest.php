<?php

namespace Eav\TestCase\Feature;


class TeslaEACT extends \Eav\Model
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

class DesignsEACT extends \Illuminate\Database\Eloquent\Model
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

class EntityAttributeCountTest extends TestCase
{
    /** @test */
    public function it_must_count_number_of_attributes()
    {
        $carCount = rand(1,5);

        $design = DesignsEACT::updateOrCreate(['name' => 'DesignModel_1']);

        for ($number = 1; $number <= $carCount; $number++) {
            TeslaEACT::create([
                'name' => 'Name' . $number,
                'sku'  => 'sku' . $number,
                'age' => rand(50, 100),
                'search' => 1
            ])->designs()->sync([$design->id]);
        }

        $designs = DesignsEACT::withCount('cars')->where('name', 'like', 'DesignModel_%')->get();
        
        foreach ($designs as $design) {
            $this->assertEquals($design->cars_count, $carCount);
        }
    }
}
