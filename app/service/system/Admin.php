<?php

namespace app\service\system;

use think\facade\Cache;
use think\facade\Request;
use maike\trait\ErrorTrait;
use maike\util\ArrUtil;
use maike\util\StrUtil;
use app\model\system\Admin as AdminModel;
use app\model\system\AdminLogin;
use app\model\system\Action as ActionModel;

class Admin
{
    use ErrorTrait;

    /**
     * 账号密码登录
     * @param array $post
     * @return bool
     */
    public function login($username, $password)
    {
        try {
            if (empty($username) || empty($password)) {
                $this->setError('用户名及密码不能为空');
                return false;
            }
            $info = AdminModel::get([['username|mobile', '=', $username]]);
            if (!$info) {
                $this->setError('用户不存在');
                return false;
            }
            if (isset($info['role_status']) && $info['role_status'] !== 1) {
                $this->setError('用户被禁用，无法登录');
                return false;
            }
            if ($info['status'] !== 1) {
                $this->setError('用户被禁用，无法登录');
                return false;
            }

            if (!password_verify($password, $info['password'])) {
                //密码错误
                $this->setError('密码错误');
                return false;
            }

            $info->tokenExpire = strtotime("+99 days");

            //生成Token
            $token = '';
            $tokenData = $info->createToken();
            if ($tokenData && isset($tokenData['token'])) {
                $token = $tokenData['token'];
            }

            //新增登录记录
            $loginLog = [
                'user_agent' => Request::header('user-agent'),
                'ip' => Request::ip(),
                'token' => $token,
                'user_id' => $info['user_id'],
                'expire_time' => time() + 86400 * 7
            ];
            AdminLogin::create($loginLog);

            return compact('info', 'token');
        } catch (\think\Exception $e) {
            $this->setError('登录发生错误' . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取管理菜单数据
     * @return array
     */
    public function getMenuData($adminInfo)
    {
        $menu = ActionModel::getMenuData();
        if (!$menu || is_null($menu) || empty($menu)) return [];
        $actionList = $adminInfo['actions'];
        $isSuper = isset($adminInfo['is_super']) && $adminInfo['is_super'] == 1 ? $adminInfo['is_super'] : 0;

        if ($isSuper != 1) {
            if ($actionList != null && !empty($actionList)) {
                $temp = [];
                foreach ($menu as $item) {
                    if ($item['required'] == 1 || empty($item['action']) || in_array($item['action'], $actionList)) {
                        $temp[] = $item;
                    }
                }
                $menu = $temp;
            }
        }

        $menu = ArrUtil::ToTree((array)$menu, "action_id");
        $new = [];
        foreach ($menu as $k => $item) {
            $newItem = [
                'key' => $item['action_id'],
                'auth' => $item['auth'],
                'title' => $item['title'],
                'icon' => $item['icon'],
                'route' => $item['route'],
                'children' => []
            ];
            if (isset($item['children']) && count($item['children']) > 0) {
                foreach ($item['children'] as $sub) {
                    $newItem['children'][] = [
                        'key' => $sub['action_id'],
                        'auth' => $sub['auth'],
                        'title' => $sub['title'],
                        'icon' => $sub['icon'],
                        'route' => $sub['route']
                    ];
                }
            }
            if ((isset($newItem['children']) && !empty($newItem['children']) && count($newItem['children']) > 0) || $item['base_auth'] == 1) {
                if (empty($newItem['route']) && count($newItem['children']) > 0) {
                    $newItem['route'] = $newItem['children'][0]['route'];
                }
            }
            $new[] = $newItem;
        }
        return $new;
    }

    /**
     * 验证权限
     * @param array $adminInfo
     * @return bool
     * @throws \maike\exception\ApiException
     */
    public function checkAuth(\maike\core\Request $request)
    {
        $adminInfo = $request->adminInfo();
        if (!$adminInfo) {
            return false;
        }

        if (intval($adminInfo['is_super']) !== 1) {
            // 非超级管理员刚检测权限
            if (!isset($adminInfo['role_id']) || intval($adminInfo['role_id']) <= 0) {
                // 无角色权限
                return false;
            }
            $actions = $adminInfo['actions'];
            $route = Request::url();
            $routeArr = StrUtil::ToArray($route, ".");
            if (is_array($routeArr) && count($routeArr) > 1) {
                $route = $routeArr[1];
            } else {
                $appName = app('http')->getName();
                $route = str_replace("/" . $appName . "/", "", $route);
            }
            //判断权限
            if (!in_array($route, $actions)) {
                return false;
            }
        }
        return true;
    }

    public function getPermissionByRole($roleId)
    {
        $cacheKey = md5('admin_role_' . $roleId);
        $actions = Cache::get($cacheKey);
        if (!$actions || $actions == null || !is_array($actions)) {
            return [];
        }
        return $actions;
    }
}
