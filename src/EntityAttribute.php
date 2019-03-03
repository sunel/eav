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
                
        $entity = $instance->findEntity($data['entity_code']);
        
        $attribute = $instance->findAttribute($data['attribute_code'], $entity);
        
        $set = $instance->findOrCreateSet($data['attribute_set'], $entity);
        
        $group = $instance->findOrCreateGroup($data['attribute_group'], $set);
        
        $instance->fill([
            'entity_id' => $entity->entity_id,
            'attribute_set_id' => $set->attribute_set_id,
            'attribute_group_id' => $group->attribute_group_id,
            'attribute_id' => $attribute->attribute_id
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
                
        $entity = $instance->findEntity($data['entity_code']);
        
        $attribute = $instance->findAttribute($data['attribute_code'], $entity);
        
        $instance->where([
            'entity_id' => $entity->entity_id,
            'attribute_id' => $attribute->attribute_id
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
    
    private function findOrCreateGroup($code, $set)
    {
        return AttributeGroup::firstOrCreate([
            'attribute_set_id' => $set->attribute_set_id,
            'attribute_group_name' => $code,
        ]);
    }
}
