<?php

namespace App\Http\Middleware;

use App\Exceptions\NotAcceptableException;
use App\Exceptions\UnsupportedMediaTypeException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForceJsonResponse
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws NotAcceptableException
     * @throws UnsupportedMediaTypeException
     */
    public function handle(Request $request, Closure $next): mixed
    {

        if ($request->header('Accept') !== 'application/vnd.api+json' && $request->header('Accept') !== '*/*') {
            return throw new NotAcceptableException();
        }
//        dump(Str::contains($request->getRequestUri(),'/storage/upload'));
//        dd();
        $hasContent = $request->isMethod(\Symfony\Component\HttpFoundation\Request::METHOD_POST) || $request->isMethod(
                Request::METHOD_PATCH
            ) || $request->isMethod(Request::METHOD_PUT);
        if(Str::contains($request->getRequestUri(),'/storage/upload')){
            $response = $next($request);

            $response->header('Content-Type', 'application/vnd.api+json');

            return $response;
        }
        if ($hasContent
            && $request->hasHeader('Content-Type')
            && $request->header('Content-Type') !== 'application/vnd.api+json' ) {
//            dd($request->header('Content-Type'));
            return throw new UnsupportedMediaTypeException();
        }
//        dd($request->header('Content-Type'));
        $response = $next($request);

        $response->header('Content-Type', 'application/vnd.api+json');

        return $response;
    }
}
