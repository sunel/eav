<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\Json\RelationshipResource;

class EntityRelationship extends RelationshipResource
{
    protected $casts = [
        'attributes' => 'attribute',
    ];

    protected function sets($request)
    {
        return [
            'data'  => $this->sets->map(function ($set) {
                $this->addInclude(new AttributeSet($set));
                return new AttributeSetIdentifier($set);
            }),
        ];
    }

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
