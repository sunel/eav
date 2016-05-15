<?php

namespace Eav;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    protected $primaryKey = 'option_id';
    
    public $timestamps = false;
    
    protected $with = ['value'];
    
    protected $fillable = [
        'attribute_id'
    ];
    
    public function value()
    {
        return $this->hasMany(AttributeOptionValue::class, 'option_id');
    }
    
    public static function add(Attribute $attribute, $options)
    {
        foreach ($options as $value) {
            $option = static::create([
                'attribute_id' => $attribute->attribute_id
            ]);
            
            AttributeOptionValue::create([
                'option_id' => $option->option_id,
                'value' => $value,
            ]);
        }
    }
}
