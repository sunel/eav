<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\ResourceCollection;

class EntityCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => parent::toArray($request),
        ];
    }

    public function with($request)
    {
        return [
            'links'    => [
                'self' => route('api.eav.entity.list'),
            ],
        ];
    }
}
