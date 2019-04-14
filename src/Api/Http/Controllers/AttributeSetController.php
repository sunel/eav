<?php

namespace Eav\Api\Http\Controllers;

use Eav\Entity;
use Eav\EntityAttribute;
use Eav\Api\Http\Resources\AttributeSet;
use Eav\Api\Http\Resources\AttributeSetCollection;
use Illuminate\Http\Request;
use ApiHelper\Http\Resources\Error;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeSetController extends Controller
{
    public function list(Request $request, $code)
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

        $sets = $entity->sets()->with($this->getIncludes($request)->all());

        $search = $request->input('filter.search', false);

        if ($search !== false) {
            $sets->where('attribute_set_name', 'like', '%' . $search . '%');
        }

        return new AttributeSetCollection(
            $this->paginate($sets)
        );
    }

    public function get(Request $request, $code, $id)
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

        $set = $entity->sets()->with($this->getIncludes($request)->all())->where('attribute_set_id', $id)->first();

        return new AttributeSet($set);
    }

    public function create(Request $request, $code)
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

        $name = $request->input('data.attributes.name');
        $set = $entity->sets()->create([
            'attribute_set_name' => $name
        ]);

        return new AttributeSet($set);
    }

    public function update(Request $request, $code, $id)
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

        $set = $entity->sets()->where('attribute_set_id', $id)->first();

        try {
            $this->updateGroup($entity, $set, $request->input('data'));
        } catch (\Exception $e) {
            return (new Error([
                'code' => '500',
                'title' => 'The backend responded with an error',
                'detail' => 'Service not responding.',
            ]))->response()
              ->setStatusCode(500);
        }

        return response(null, 204);
    }


    protected function updateGroup($entity, $set, $groups)
    {
        foreach ($groups as $item) {
            $group = $set->groups()->findOrFail($item['id']);

            $group->fill($item['attributes'])->save();

            $this->updateRelations($entity, $set, $group, $item['relationships']);
        }
    }

    protected function updateRelations($entity, $set, $group, $relations)
    {
        foreach ($relations as $relation => $values) {
            call_user_func_array([$this, 'update'.ucfirst($relation)], [$entity, $set, $group, $values['data']]);
        }
    }

    protected function updateAttributes($entity, $set, $group, $attributes)
    {
        $attributes = collect($attributes)->mapWithKeys(function ($attribute) {
            return [ $attribute['id'] => [ 'sequence'=> $attribute['attributes']['sequence'] ]];
        })->all();

        EntityAttribute::sync($entity, $set, $group, $attributes);
    }

    /**
     * Validate the given request.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function validateData(Request $request)
    {
        $request->validate([
            'data.attributes.name' => 'required|unique:attribute_sets,attribute_set_name',
        ], [], [
            'data.attributes.name' => 'name',
        ]);
    }
}
