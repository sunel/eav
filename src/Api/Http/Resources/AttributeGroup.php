<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\Resource;

class AttributeGroup extends Resource
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
            'attributes'    => [
                'name'         => $this->name(),
                'sequence'     => $this->sequence,
            ],
            'relationships' => $this->loadRelation(new AttributeGroupRelationship($this->resource), $request),
        ];
    }

    public function with($request)
    {
        return [
            'links'    => [
                'self' => route('api.eav.group.get', [$request->route('code'), $request->route('setId'), $this->getKey()]),
            ],
        ];
    }
}
