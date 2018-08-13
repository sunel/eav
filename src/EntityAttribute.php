<?php

namespace Eav;

use Illuminate\Database\Eloquent\Model;

class EntityAttribute extends Model
{
    /**
     * @{inheriteDoc}
     */
    protected $primaryKey = 'attribute_id';
    
    /**
     * @{inheriteDoc}
     */
    public $timestamps = false;
    
    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'entity_id', 'attribute_set_id', 'attribute_group_id',
        'attribute_id'
    ];
    
    /**
     * Attach the attribute to the entity.
     *
     * @param  array $data
     * @return bool
     */
    public static function map(array $data)
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
    
    
    /**
     * Un attach the attribute to the entity.
     *
     * @param  array $data
     * @return bool
     */
    public static function unmap(array $data)
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
            return Entity::findByCode($code);
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
