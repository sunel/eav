<?php

namespace Eav\Api\Http\Controllers;

use Eav\Entity;
use Eav\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use ApiHelper\Http\Concerns\InteractsWithRequest;
use ApiHelper\Http\Resources\Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, InteractsWithRequest;

    protected function paginate($model)
    {
        $size = (int) request()->input('page.size', 25);
        $size = $size > 25 ? 25 : $size;
        return $model->paginate($size, ['*'], 'page.number')
            ->setPageName('page[number]')
            ->appends(Arr::except(request()->input(), 'page.number'));
    }

    public function getEntity($code)
    {
        try {
            return Entity::findByCode($code);
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException((new Error([
                'code' => '101',
                'title' => 'Invalid Code',
                'detail' => 'Given Code does not exist.',
            ]))->response()
              ->setStatusCode(404));
        }
    }

    public function getAttribute($id, $code)
    {
        try {
            return Attribute::findByCode($id, $code);
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException((new Error([
                'code' => '101',
                'title' => 'Invalid Attribute Code',
                'detail' => 'Given Attribute Code does not exist.',
            ]))->response()
              ->setStatusCode(404));
        }
    }
}
