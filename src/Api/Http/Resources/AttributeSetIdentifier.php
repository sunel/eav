<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\Resource;

class AttributeSetIdentifier extends Resource
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
            'type'          => 'attribute_set',
            'id'            => (string) $this->getKey(),
            'relationships' => $this->loadRelation(new AttributeSetRelationship($this->resource), $request),
        ];
    }
}
