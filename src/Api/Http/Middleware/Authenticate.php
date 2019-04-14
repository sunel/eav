<?php

namespace Eav\Api\Http\Middleware;

use Eav\Api\Registery;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, $next)
    {
        return Registery::check($request) ? $next($request) : abort(403);
    }
}
