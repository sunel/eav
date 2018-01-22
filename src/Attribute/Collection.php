<?php

namespace Eav\Attribute;

use Validator;
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
     * Get the keys of the collection items.
     *
     * @return static
     */
    public function code()
    {
        return $this->keys();
    }


    /**
     * Get the keys of the collection items.
     *
     * @return static
     */
    public function patch()
    {
        return new static(array_reduce($this->items, function ($result, $item) {
            $result[$item->getAttributeCode()] = $item;

            return $result;
        }, []));
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
