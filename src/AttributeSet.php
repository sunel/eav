<?php

namespace Eav;

use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model
{
    protected $primaryKey = 'attribute_set_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'attribute_set_name' , 'entity_id'
    ];
    
    public function eavAttributes()
    {
        return $this->hasManyThrough(Attribute::class, EntityAttribute::class, 'attribute_set_id', 'attribute_id');
    }
    
    public function eavAttributeGroup()
    {
        return $this->hasMany(AttributeGroup::class);
    }
}
