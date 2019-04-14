<?php

namespace Eav\Api\Http\Controllers;

use Eav\Entity;
use Eav\Attribute as AttributeModel;
use Eav\Api\Http\Resources\Attribute;
use Eav\Api\Http\Resources\AttributeCollection;
use ApiHelper\Http\Resources\Error;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeController extends Controller
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

        if ((int) $request->input('filter.unassigned', 0) === 1) {
            $set = $request->input('filter.set', null);
            $attributes = $entity->unassignedAttributes($set);
        } else {
            $attributes = $entity->attributes();
        }

        $search = $request->input('filter.search', false);

        if ($search !== false) {
            $attributes->where(function ($query) use ($search) {
                $query->where('attribute_code', 'like', '%' . $search . '%')
                    ->orWhere('frontend_label', 'like', '%' . $search . '%');
            });
        }

        return new AttributeCollection(
            $this->paginate($attributes->with($this->getIncludes($request)->all()))
        );
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

        try {
            $request->validate([
                'data.attributes.code' => Rule::unique('attributes', 'attribute_code')->where('entity_id', $entity->getKey())
            ], [], [
                'data.attributes.code' => 'code',
            ]);
        } catch (ValidationException $e) {
            return (new Error($e->validator))
                ->response()
                    ->setStatusCode(400);
        }

        $attributes = $request->input('data.attributes');

        $data = array_only($attributes, [
            'frontend_label', 'frontend_type',
            'is_required', 'is_filterable',
            'is_searchable', 'backend_type'
        ]);

        $data['attribute_code'] = $attributes['code'];
        $data['entity_code'] = $code;

        $data['default_value'] = array_get($attributes, 'default_value', '');
        

        return new Attribute(AttributeModel::add($data));
    }

    public function get(Request $request, $code, $id)
    {
        try {
            $entity = Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Entity Code',
                'detail' => 'Given Entity Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }


        try {
            $attribute = AttributeModel::findByCode($id, $code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Attribute Code',
                'detail' => 'Given Attribute Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }

        return new Attribute($attribute);
    }

    public function update(Request $request, $code, $id)
    {
        try {
            $entity = Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Entity Code',
                'detail' => 'Given Entity Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }


        try {
            $attribute = AttributeModel::findByCode($id, $code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Attribute Code',
                'detail' => 'Given Attribute Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }

        $attributes = $request->input('data.attributes');

        $data = array_only($attributes, [
            'frontend_label', 'frontend_type',
            'is_required', 'is_filterable',
            'is_searchable', 'backend_type'
        ]);

        $data['default_value'] = array_get($attributes, 'default_value', '');

        $attribute->fill($data)->save();
        
        return new Attribute($attribute);
    }

    public function remove(Request $request, $code, $id)
    {
        try {
            $entity = Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Entity Code',
                'detail' => 'Given Entity Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }


        try {
            $attribute = AttributeModel::findByCode($id, $code);
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Attribute Code',
                'detail' => 'Given Attribute Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }

        $attribute->delete();

        return response(null, 204);
    }

    /**
     * Validate the given request.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function validateData(Request $request)
    {
        $request->validate([
            'data.attributes.code' => 'required|max:255',
            'data.attributes.frontend_label' => 'required',
            'data.attributes.frontend_type' => [
                'required',
                Rule::in(config('eav.elementTypes', [])),
            ],
            'data.attributes.is_required' => 'required|boolean',
            'data.attributes.is_filterable' => 'required|boolean',
            'data.attributes.is_searchable' => 'required|boolean',
            'data.attributes.backend_type' => [
                'required',
                Rule::in(config('eav.fieldTypes', [])),
            ],
        ], [], [
            'data.attributes.code' => 'code',
            'data.attributes.frontend_label' => 'frontend label',
            'data.attributes.frontend_type' => 'frontend type',
            'data.attributes.is_required' => 'required',
            'data.attributes.backend_type' => 'backend type',
        ]);
    }
}
