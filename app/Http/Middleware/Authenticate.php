<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{

//    /**
//     * @param Request $request
//     * @param Closure $next
//     * @param         ...$guards
//     * @return string|null
//     */
//    public function handle($request, Closure $next, ...$guards)
//    {
//        $guards = empty($guards) ? [null] : $guards;
//
//        foreach ($guards as $guard) {
//            if (!Auth::guard($guard)->check()) {
//                return response('Unauthorized.', 401);
//            }
//        }
//
//        return $next($request);
//    }
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return response('Unauthorized.', 401);
        }
    }
}
