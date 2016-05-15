<?php

namespace Eav;

use Illuminate\Database\Eloquent\Model;

class EntityAttribute extends Model
{
    protected $primaryKey = 'attribute_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'entity_id', 'attribute_set_id', 'attribute_group_id',
        'attribute_id'
    ];
    
    public static function map($data)
    {
        $instance = new static;
                
        $eavEntity = $instance->findEntity($data['entity_code']);
        
        $eavAttribute = $instance->findAttribute($data['attribute_code'], $eavEntity);
        
        $eavAttributeSet = $instance->findOrCreateSet($data['attribute_set'], $eavEntity);
        
        $eavAttributeGroup = $instance->findOrCreateGroup($data['attribute_group'], $eavAttributeSet);
        
        $instance->fill([
            'entity_id' => $eavEntity->entity_id,
            'attribute_set_id' => $eavAttributeSet->attribute_set_id,
            'attribute_group_id' => $eavAttributeGroup->attribute_group_id,
            'attribute_id' => $eavAttribute->attribute_id
        ])->save();
    }
    
    
    public static function unmap($data)
    {
        $instance = new static;
                
        $eavEntity = $instance->findEntity($data['entity_code']);
        
        $eavAttribute = $instance->findAttribute($data['attribute_code'], $eavEntity);
        
        $instance->where([
            'entity_id' => $eavEntity->entity_id,
            'attribute_id' => $eavAttribute->attribute_id
        ])->delete();
    }
    
    private function findEntity($code)
    {
        try {
            return Entity::where('entity_code', '=', $code)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Unable to load Entity : ".$code);
        }
    }
        
    private function findAttribute($code, $entity)
    {
        try {
            return Attribute::where([
                'attribute_code'=> $code,
                'entity_id' => $entity->entity_id,
            ])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Unable to load Attribute : ".$code);
        }
    }

    private function findOrCreateSet($code, $entity)
    {
        return AttributeSet::firstOrCreate([
            'attribute_set_name' => $code,
            'entity_id' => $entity->entity_id,
        ]);
    }
    
    private function findOrCreateGroup($code, $attributeSet)
    {
        return AttributeGroup::firstOrCreate([
            'attribute_set_id' => $attributeSet->attribute_set_id,
            'attribute_group_name' => $code,
        ]);
    }
}
