<?php

namespace Eav;

use Eav\Attribute\Option\Collection;
use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    protected $primaryKey = 'option_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'attribute_id', 'label', 'value'
    ];
    
    public static function add(Attribute $attribute, $options)
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
