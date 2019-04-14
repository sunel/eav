<?php

namespace Eav\Api\Http\Controllers;

use Eav\Entity;
use Eav\Api\Http\Resources\AttributeGroup;
use Eav\Api\Http\Resources\AttributeGroupCollection;
use Eav\Api\Http\Resources\AttributeCollection;
use Illuminate\Http\Request;
use ApiHelper\Http\Resources\Error;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeGroupController extends Controller
{
    public function list(Request $request, $code, $setId)
    {
        try {
            $entity = Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Code',
                'detail' => 'Given Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();

        return new AttributeGroupCollection(
            $this->paginate($set->groups()->with($this->getIncludes($request)->all()))
        );
    }

    public function get(Request $request, $code, $setId, $id)
    {
        try {
            $entity = Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Code',
                'detail' => 'Given Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();

        $group = $set->groups()->with($this->getIncludes($request)->all())->where('attribute_group_id', $id)->first();

        return new AttributeGroup($group);
    }

    public function listAttributes(Request $request, $code, $setId, $id)
    {
        try {
            $entity = Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Code',
                'detail' => 'Given Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();

        $group = $set->groups()->with(['attributes'])->where('attribute_group_id', $id)->first();


        return new AttributeCollection($group->attributes);
    }

    public function create(Request $request, $code, $setId)
    {
        try {
            $this->validateData($request);
        } catch (ValidationException $e) {
            return (new Error($e->validator))
                ->response()
                    ->setStatusCode(400);
        }

        try {
            $entity = Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Code',
                'detail' => 'Given Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }

        $set = $entity->sets()->where('attribute_set_id', $setId)->first();

        $name = $request->input('data.attributes.name');
        $group = $set->groups()->create([
            'attribute_group_name' => $name
        ]);

        return new AttributeGroup($group);
    }

    /**
     * Validate the given request.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function validateData(Request $request)
    {
        $request->validate([
            'data.attributes.name' => 'required|unique:attribute_groups,attribute_group_name',
        ], [], [
            'data.attributes.name' => 'name',
        ]);
    }
}
