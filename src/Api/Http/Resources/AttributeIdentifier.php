<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\Resource;

class AttributeIdentifier extends Resource
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
            'type'  => 'attribute',
            'id'    => (string) $this->attributeId(),
        ];
    }
}
