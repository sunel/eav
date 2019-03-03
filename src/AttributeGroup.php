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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'sequence' => 0,
    ];
    
    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'attribute_set_id', 'attribute_group_name', 'sequence'
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
     * Set the name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['attribute_group_name'] = $value;
    }

    /**
     * Get a has-many-through relation attributes by sequence order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function attributes()
    {   
        return $this->hasManyThrough(Attribute::class, EntityAttribute::class, 'attribute_group_id', 'attribute_id')
            ->select(['attributes.*', 'entity_attributes.sequence'])
            ->orderBy('entity_attributes.sequence');
    }
}
