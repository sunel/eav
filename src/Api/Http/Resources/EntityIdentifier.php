<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\Resource;

class EntityIdentifier extends Resource
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
            'type'          => 'entity',
            'id'            => (string) $this->id,
            'relationships' => $this->loadRelation(new EntityRelationship($this->resource), $request),
        ];
    }
}
