<?php

namespace Eav\Attribute\Option;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toOptions()
    {
        return array_reduce($this->items, function ($result, $item) {
            $result[$item->value] = $item->label;
            return $result;
        }, array());
    }
}
