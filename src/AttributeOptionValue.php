<?php

namespace Eav;

use Eav\Attribute\Option\Collection;
use Illuminate\Database\Eloquent\Model;

class AttributeOptionValue extends Model
{
    protected $primaryKey = 'value_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'option_id', 'value'
    ];
    
    
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
