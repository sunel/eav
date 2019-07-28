<?php

namespace Eav\Api\Http\Controllers;

use Eav\Entity;
use Eav\Api\Http\Resources\AttributeGroup;
use Eav\Api\Http\Resources\AttributeGroupCollection;
use Eav\Api\Http\Resources\AttributeCollection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ApiHelper\Http\Resources\Error;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeGroupController extends Controller
{
    public function list(Request $request, $code, $setId)
    {
        $entity = $this->getEntity($code);

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();

        return new AttributeGroupCollection(
            $this->paginate($set->groups()->with($this->getIncludes($request)->all()))
        );
    }

    public function get(Request $request, $code, $setId, $id)
    {
        $entity = $this->getEntity($code);

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();

        $group = $set->groups()->with($this->getIncludes($request)->all())->where('attribute_group_id', $id)->first();

        return new AttributeGroup($group);
    }

    public function listAttributes(Request $request, $code, $setId, $id)
    {
        $entity = $this->getEntity($code);

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();

        $group = $set->groups()->with(['attributes'])->where('attribute_group_id', $id)->first();


        return new AttributeCollection($group->attributes);
    }

    public function create(Request $request, $code, $setId)
    {
        try {
            $this->validateData($request, $setId);
        } catch (ValidationException $e) {
            return (new Error($e->validator))
                ->response()
                    ->setStatusCode(400);
        }

        $entity = $this->getEntity($code);

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();

        $name = $request->input('data.attributes.name');
        $group = $set->groups()->create([
            'attribute_group_name' => $name
        ]);

        return new AttributeGroup($group);
    }

    public function update(Request $request, $code, $setId, $id)
    {
        try {
            $this->validateData($request, $setId);
        } catch (ValidationException $e) {
            return (new Error($e->validator))
                ->response()
                    ->setStatusCode(400);
        }

        $entity = $this->getEntity($code);

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();
        
        $group = $set->groups()->where('attribute_group_id', $id)->first();

        $name = $request->input('data.attributes.name');

        $group->fill([
            'attribute_group_name' => $name
        ])->save();

        return new AttributeGroup($group);
    }

    public function remove(Request $request, $code, $setId, $id)
    {
        $entity = $this->getEntity($code);

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();
        
        $group = $set->groups()->where('attribute_group_id', $id)->first();

        $group->delete();

        return response(null, 204);
    }

    /**
     * Validate the given request.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $setId
     */
    protected function validateData(Request $request, $setId)
    {
        $request->validate([
            'data.attributes.name' => [
                'required',
                Rule::unique('attribute_groups', 'attribute_group_name')->where(function ($query) use($setId) {
                    return $query->where('attribute_set_id', $setId);
            })],
        ], [], [
            'data.attributes.name' => 'name',
        ]);
    }
}
