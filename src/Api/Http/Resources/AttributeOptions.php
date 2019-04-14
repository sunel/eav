<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\Resource;

class AttributeOptions extends Resource
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
            'type'          => 'attribute_options',
            'id'            => (string) $this->getKey(),
            'attributes'    => [
                'label'         => $this->label,
                'value'   => $this->value,
            ]
        ];
    }
}
