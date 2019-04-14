<?php

namespace Eav\Api\Http\Controllers;

use Eav\Entity;
use Eav\Api\Http\Resources\Entity as EntityResource;
use Eav\Api\Http\Resources\EntityCollection;
use ApiHelper\Http\Resources\Error;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EntityController extends Controller
{
    public function list(Request $request)
    {
        $entities = Entity::with($this->getIncludes($request)->all());

        $search = $request->input('filter.search', false);

        if ($search !== false) {
            $entities->where('entity_code', 'like', '%' . $search . '%');
        }

        return new EntityCollection(
            $this->paginate($entities)
        );
    }

    public function get(Request $request, $code)
    {
        try {
            $entity = Entity::findByCode($code);
            $entity->load($this->getIncludes($request)->all());
        } catch (ModelNotFoundException $e) {
            return (new Error([
                'code' => '101',
                'title' => 'Invalid Code',
                'detail' => 'Given Code does not exist.',
            ]))->response()
              ->setStatusCode(404);
        }

        return new EntityResource($entity);
    }
}
