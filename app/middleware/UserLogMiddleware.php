<?php

namespace maike\middlewares;

use Closure;
use think\Response;
use think\facade\Log;
use maike\interfaces\MiddlewareInterface;
use maike\core\Request;
use app\model\SystemLog as SystemLogModel;

/**
 * 系统日志记录中间件
 * @package maike\middlewares
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
            $model->record($request->userId(), $request->userInfo()['username']);
        } catch (\Throwable $e) {
            Log::write($e->getMessage(), "error");
        }

        return $next($request);
    }
}
