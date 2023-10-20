<?php

namespace app\model\system;

use app\model\BaseModel;
use think\Exception;
use maike\core\Request;
use think\facade\Log;

/**
 * 后台行为日志
 */
class SystemLog extends BaseModel
{
    // 需要记录的动作（Action）
    protected static $NotLogAction = ['select_option', 'all', 'group_list', 'list'];

    /**
     * 记录行为日志
     */
    public static function record(Request $request)
    {
        try {
            $curAction = $request->action();
            if (!in_array($curAction, SystemLog::$NotLogAction)) {
                $module = app('http')->getName();
                $actionUrl = substr($request->url(), 1);
                $actionInfo = Action::getByAction($actionUrl);
                $actionId = 0;

                $operatorName = '';
                $operatorId = 0;

                $adminInfo = $request->adminInfo();
                if ($adminInfo) {
                    $operatorName = $adminInfo['username'] . "(" . $adminInfo['name'] . ")";
                    $operatorId = $adminInfo['admin_id'];
                }

                
                
                $title = "-未知-";
                $content = "";
                if ($actionInfo && count($actionInfo) > 0) {
                    $actionInfo = array_values((array)$actionInfo);
                    $title = $actionInfo[0]['title'];
                    $actionId = $actionInfo[0]['action_id'];
                    $content = $actionInfo[0]['desc'];
                } else if ($actionUrl == 'user/login') {
                    $title = "登录";
                    $content = "管理员登录";
                }
                // 日志数据
                $params = $request->param();
                if (isset($params['controller'])) unset($params['controller']);
                if (isset($params['action'])) unset($params['action']);
                if (isset($params['password'])) $params['password'] = "****";
                $data = [
                    'user_name' => $username,
                    'user_realname' => $realname,
                    'module' => $module,
                    'action_id' => $actionId,
                    'action' => $actionUrl,
                    'method' => $request->method(),
                    'url' => $request->url(true), // 获取完成URL
                    'params' => $params ? json_encode($params) : '',
                    'title' => empty($title) ? '【未知】' : $title,
                    'desc' => empty($content) ? '【未知】' : $content,
                    'ip' => $request->ip(),
                    'user_agent' => $request->server('HTTP_USER_AGENT'),
                    'operator_id' => $creatorId,
                    'org_id' => $createOrgId,
                    'create_time' => time()
                ];
                // 日志入库
                static::create($data);
            }
        } catch (Exception $e) {
            $log = $e->getMessage() . " | " . $e->getCode() . " | " . $e->getFile() . " | " . $e->getLine();
            Log::write($log, "error");
        }
    }
}
