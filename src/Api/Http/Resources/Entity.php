<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\Resource;

class Entity extends Resource
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
            'id'            => (string) $this->getKey(),
            'attributes'    => [
                'entity_code'         => $this->code(),
                'entity_class'   => $this->entity_class,
                'entity_table'   => $this->entity_table,
                'default_attribute_set_id'   => $this->default_attribute_set_id,
                'additional_attribute_table'   => $this->additional_attribute_table,
                'is_flat_enabled'   => $this->is_flat_enabled,
            ],
            'relationships' => $this->loadRelation(new EntityRelationship($this->resource), $request),
        ];
    }

    public function with($request)
    {
        return [
            'links'    => [
                'self' => route('api.eav.entity.get', $this->code()),
            ]
        ];
    }
}
