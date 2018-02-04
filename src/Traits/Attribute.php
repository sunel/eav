<?php

namespace Eav\Traits;

use Eav\Attribute\Collection;

trait Attribute
{
    protected static $attributesCollection = null;
    
    protected static $attributesCollectionKeys = [];
        
    public function loadAttributes($attributes = [], $static = false, $required = false)
    {
        $attributes = array_unique($attributes);

        $alreadyLoadedAttkeys = array_intersect(static::$attributesCollectionKeys, $attributes);
        
        if (count($alreadyLoadedAttkeys) && count($alreadyLoadedAttkeys) == count($attributes)) {
            return static::$attributesCollection->intersectKeys($attributes);
        } elseif (count($alreadyLoadedAttkeys) && count($alreadyLoadedAttkeys) < count($attributes)) {
            $newAttkeys = array_diff($attributes, static::$attributesCollectionKeys);
            $loadedAttributes = $this->fetchAttributes($newAttkeys, $static);

            static::$attributesCollection = static::$attributesCollection->merge($loadedAttributes);

            $loadedAttributes = static::$attributesCollection->intersectKeys($attributes);
        } else {
            $loadedAttributes = $this->fetchAttributes($attributes, $static, $required);
            static::$attributesCollection = $loadedAttributes;
        }
        
        static::$attributesCollectionKeys = array_merge(static::$attributesCollectionKeys, $loadedAttributes->code()->toArray());
                
        return $loadedAttributes;
    }
    
    protected function fetchAttributes($attributes = [], $static = false, $required = false)
    {
        $loadedAttributes = $this->baseEntity()
            ->attributes()
            ->where(function ($query) use ($static, $required, $attributes) {
                if (!empty($attributes)) {
                    $query->orWhereIn('attribute_code', $attributes);
                }
                
                if ($static) {
                    $query->orWhere('backend_type', 'static');
                }
                if ($required) {
                    $query->orWhere('is_required', 1);
                }
            })->get()->patch();


        return $loadedAttributes;
    }
    
    public function getMainTableAttribute($loadedAttributes)
    {
        $mainTableAttributeCollection = $loadedAttributes->filter(function ($attribute) {
            return $attribute->isStatic();
        });
        
        $mainTableAttribute = $mainTableAttributeCollection->code()->toArray();
        
        $mainTableAttribute[] = 'entity_id';
        $mainTableAttribute[] = 'attribute_set_id';
        
        return $mainTableAttribute;
    }
}
