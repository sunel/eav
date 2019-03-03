<?php

namespace Eav;

use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model
{
    /**
     * @{inheriteDoc}
     */
    protected $primaryKey = 'attribute_set_id';
    
    /**
     * @{inheriteDoc}
     */
    public $timestamps = false;
    
    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'attribute_set_name' , 'entity_id'
    ];

    /**
     * Proxy to get the attribute set name.
     *
     * @return string
     */
    public function name()
    {
        return $this->getAttribute('attribute_set_name');
    }
    
    /**
     * Define a has-many-through relationship for attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function attributes()
    {
        return $this->hasManyThrough(Attribute::class, EntityAttribute::class, 'attribute_set_id', 'attribute_id');
    }

    /**
     * @alias attributeGroup()
     */
    public function groups()
    {
        return $this->attributeGroup();
    }

    /**
     * Define a one-to-many relationship for attribute group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributeGroup()
    {
        return $this->hasMany(AttributeGroup::class, 'attribute_set_id')
            ->orderBy('attribute_groups.sequence');;
    }
}
