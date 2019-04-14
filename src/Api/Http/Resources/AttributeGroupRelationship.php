<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\IncludeRegistery;
use ApiHelper\Http\Resources\Json\RelationshipResource;

class AttributeGroupRelationship extends RelationshipResource
{
    protected $casts = [
        'attributes' => 'attribute',
    ];

    protected function attribute($request)
    {
        return [
            'data'  => $this->attributes->map(function ($attribute) {
                $this->addInclude(new Attribute($attribute));
                return new AttributeIdentifier($attribute);
            }),
        ];
    }
}
