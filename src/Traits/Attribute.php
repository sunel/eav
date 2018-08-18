<?php

namespace Eav\Traits;

use Cache;
use Eav\Attribute\Collection;

trait Attribute
{
    protected static $attributesCollection = null;
    
    protected static $attributesCollectionKeys = [];
        
    public function loadAttributes($attributes = [], $static = false, $required = false)
    {
        $attributes = collect($attributes)->unique();

        if($attributes->isEmpty()) {
            $this->saveAttribute(
                $this->fetchAttributes([], $static, $required)
            );
        } else {
            $newAttribute = $attributes->diff(static::$attributesCollectionKeys);
            if($newAttribute->isNotEmpty()) {
                $this->saveAttribute(
                    $this->fetchAttributes($newAttribute->all(), $static, $required)
                );
            }
        }         

        if($attributes->isEmpty()) {
            return static::$attributesCollection;
        }

        return static::$attributesCollection->only($attributes->all())->patch();
    }

    protected function saveAttribute(Collection $loadedAttributes)
    {
        if(static::$attributesCollection === null) {
            static::$attributesCollection = $loadedAttributes;
        } else {
            static::$attributesCollection = static::$attributesCollection->merge($loadedAttributes);
        }

        static::$attributesCollectionKeys = static::$attributesCollection->code()->toArray();
    }
    
    protected function fetchAttributes($attributes = [], $static = false, $required = false)
    {
        $query = $this->baseEntity()
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
            }); 
        $cacheKey = md5(implode('|', $attributes)."|{$query->toSql()}");    
        return Cache::remember($cacheKey, 10, function () use ($query) {
            return $query->get()->patch();
        });
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
