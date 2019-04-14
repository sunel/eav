<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\Resource;

class AttributeSet extends Resource
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
            'attributes'    => [
                'name'         => $this->name(),
            ],
            'relationships' => $this->loadRelation(new AttributeSetRelationship($this->resource), $request),
        ];
    }

    public function with($request)
    {
        return [
            'links'    => [
                'self' => route('api.eav.set.get', [$request->route('code'), $this->getKey()]),
            ],
        ];
    }
}
