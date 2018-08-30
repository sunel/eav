<?php

namespace Eav;

use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    /**
     * @{inheriteDoc}
     */
    protected $primaryKey = 'attribute_group_id';
    
    /**
     * @{inheriteDoc}
     */
    public $timestamps = false;
    
    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'attribute_set_id', 'attribute_group_name'
    ];

    /**
     * Proxy to get the attribute group name.
     * 
     * @return string
     */
    public function name()
    {
        return $this->getAttribute('attribute_group_name');
    }    

    /**
     * Define a has-many-through relationship for attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function attributes()
    {
        return $this->hasManyThrough(Attribute::class, EntityAttribute::class, 'attribute_group_id', 'attribute_id');
    }
}
