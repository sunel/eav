<?php

namespace Eav\Api\Http\Resources;

use ApiHelper\Http\Resources\IncludeRegistery;
use ApiHelper\Http\Resources\Json\RelationshipResource;

class AttributeSetRelationship extends RelationshipResource
{
    protected $casts = [
        'attributes' => 'attribute',
    ];

    protected function groups($request)
    {
        return [
            'data'  => $this->groups->map(function ($group) {
                $this->addInclude(new AttributeGroup($group));
                return new AttributeGroupIdentifier($group);
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
