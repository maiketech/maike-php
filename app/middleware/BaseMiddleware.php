<?php

namespace app\middleware;

use app\Request;
use maike\interfaces\MiddlewareInterface;

class BaseMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next, bool $force = true)
    {
        if (!Request::hasMacro('userId')) {
            Request::macro('userId', function () {
                return 0;
            });
        }

        if (!Request::hasMacro('customerId')) {
            Request::macro('customerId', function () {
                return 0;
            });
        }

        if (!Request::hasMacro('doctorId')) {
            Request::macro('doctorId', function () {
                return 0;
            });
        }

        return $next($request);
    }
}
