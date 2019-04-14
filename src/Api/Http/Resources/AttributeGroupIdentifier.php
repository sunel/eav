<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\Resource;

class AttributeGroupIdentifier extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type'          => 'attribute_group',
            'id'            => (string) $this->getKey(),
            'relationships' => $this->loadRelation(new AttributeGroupRelationship($this->resource), $request),
        ];
    }
}
