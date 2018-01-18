<?php

namespace Eav\Attribute;

use Validator;
use Eav\Attribute;
use Eav\Attribute\Validator as AttributeValidator;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{
    
    /**
     * Validate the data.
     *
     * @param  mixed  $data
     * @return array
     */
    public function validate($data)
    {
        $rules = [];
        $classRules = [];
        $this->each(function ($attribute) use (&$rules, &$classRules) {
            if ($attribute->getAttribute('is_required')) {
                if ($class = $attribute->getAttribute('required_validate_class')) {
                    $rules[$attribute->getAttributeCode()] = $class;
                } else {
                    $rules[$attribute->getAttributeCode()] = 'required';
                }
                if ($attribute->getAttribute('backend_class')) {
                    $rules[$attribute->getAttributeCode()] = [$rules[$attribute->getAttributeCode()], $attribute->getBackend()];
                }
            }
        });
        
        $attributeValidator = Validator::make($data, $rules);       

        if ($attributeValidator->fails()) {
            throw new ValidationException($attributeValidator);
        }
    }
    
    
    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed  $items
     * @return array
     */
    protected function getArrayableItems($items)
    {
        $items = array_reduce($items, function ($result, $item) {
            if (is_a($item, Attribute::class)) {
                $result[$item->getAttributeCode()] = $item;
            } else {
                $result[] = $item;
            }
            return $result;
        }, array());

        return $items;
    }
    
    
    /**
     * Intersect the collection with the given items.
     *
     * @param  mixed  $items
     * @return static
     */
    public function intersectKeys($keys)
    {
        return new static(array_intersect_key($this->items, array_flip($keys)));
    }
    
    
    /**
     * Merge the collection with the given items.
     *
     * @param  \ArrayAccess|array  $items
     * @return static
     */
    public function merge($items)
    {
        $dictionary = $this->getDictionary();

        foreach ($items as $item) {
            $dictionary[$item->getAttributeCode()] = $item;
        }

        return new static($dictionary);
    }
    
    
    /**
     * Get a dictionary keyed by primary keys.
     *
     * @param  \ArrayAccess|array|null  $items
     * @return array
     */
    public function getDictionary($items = null)
    {
        $items = is_null($items) ? $this->items : $items;

        $dictionary = [];

        foreach ($items as $value) {
            $dictionary[$value->getAttributeCode()] = $value;
        }

        return $dictionary;
    }
}
