<?php

namespace app\middleware;

use Closure;
use think\Response;
use think\facade\Log;
use maike\interface\MiddlewareInterface;
use maike\core\Request;
use app\model\system\SystemLog as SystemLogModel;

/**
 * 系统日志记录中间件
 * @package app\middleware
 */
class AdminLogMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        //操作日志记录
        try {
            $model = app()->make(SystemLogModel::class);
            $model->record($request);
        } catch (\Throwable $e) {
            Log::write($e->getMessage(), "error");
        }

        return $next($request);
    }
}
