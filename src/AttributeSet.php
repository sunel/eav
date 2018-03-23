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
    
    public function attributes()
    {
        return $this->hasManyThrough(Attribute::class, EntityAttribute::class, 'attribute_set_id', 'attribute_id');
    }
    
    public function attributeGroup()
    {
        return $this->hasMany(AttributeGroup::class, 'attribute_set_id');
    }
}
