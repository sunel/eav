<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\RelationshipResource;

class AttributeRelationship extends RelationshipResource
{
    protected $casts = [
        'options' => 'optionValues'
    ];

    protected function options($request)
    {
        return [
            'data'  =>  $this->resource->options(),
        ];
    }
}
