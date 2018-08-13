<?php

namespace Eav;

use Eav\Attribute\Option\Collection;
use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    /**
     * @{inheriteDoc}
     */
    protected $primaryKey = 'option_id';
    
    /**
     * @{inheriteDoc}
     */
    public $timestamps = false;
    
    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'attribute_id', 'label', 'value'
    ];
    
    /**
     * Add options for the attribute.
     *
     * @param Attribute $attribute
     * @param array     $options
     *
     * @return void
     */
    public static function add(Attribute $attribute, array $options)
    {
        foreach ($options as $value => $label) {
            $option = static::create([
                'attribute_id' => $attribute->attribute_id,
                'label' => $label,
                'value' => $value
            ]);
        }
    }

    /**
     * Remove options for the attribute.
     *
     * @param Attribute $attribute
     * @param array     $options
     *
     * @return void
     */
    public static function remove(Attribute $attribute, array $options)
    {
        $instance = new static;
        
        foreach ($options as $value => $label) {
            $instance->where([
                'attribute_id' => $attribute->attribute_id,
                'label' => $label,
                'value' => $value
            ])->delete();
        }
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }
}
