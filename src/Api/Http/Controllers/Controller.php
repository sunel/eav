<?php

namespace Eav\Api\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ApiHelper\Http\Concerns\InteractsWithRequest;
use Eav\Api\Http\Middleware\Authenticate;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, InteractsWithRequest;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authenticate::class);
    }

    protected function paginate($model)
    {
        $size = (int) request()->input('page.size', 25);
        $size = $size > 25 ? 25 : $size;
        return $model->paginate($size, ['*'], 'page.number')
            ->setPageName('page[number]')
            ->appends(array_except(request()->input(), 'page.number'));
    }
}
