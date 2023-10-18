<?php

namespace maike\middlewares;

use maike\core\Request;
use maike\interfaces\MiddlewareInterface;

class BaseMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next, bool $force = true)
    {
        if (!Request::hasMacro('adminId')) {
            Request::macro('adminId', function () {
                return 0;
            });
        }

        if (!Request::hasMacro('userId')) {
            Request::macro('userId', function () {
                return 0;
            });
        }

        return $next($request);
    }
}
