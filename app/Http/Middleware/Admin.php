<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Admin
{


    public function handle($request, Closure $next)
    {

        if (!Auth::user()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $user = Auth::user();
        $roles = ['manager','commission','super_admin'];
        if (! $user->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }
        return $next($request);


    }
}
