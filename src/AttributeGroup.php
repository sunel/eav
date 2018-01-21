<?php

namespace Eav;

use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    protected $primaryKey = 'attribute_group_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'attribute_set_id', 'attribute_group_name'
    ];
    
    public function attributes()
    {
        return $this->hasManyThrough(Attribute::class, EntityAttribute::class, 'attribute_group_id', 'attribute_id');
    }
}
