<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JsonApi
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @throws PreconditionFailedHttpException|UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Content-Type') !== 'application/vnd.api+json') {
            throw new PreconditionFailedHttpException('Invalid Content-Type header');
        }

        return $next($request);
    }
}
