<?php

namespace maike\service\system;

use think\facade\Cache;
use think\facade\Request;
use maike\exception\ApiException;
use maike\service\BaseService;

class UserCheckPermission extends BaseService
{
    /**
     * 验证权限
     * @param array $userInfo
     * @return bool
     * @throws \maike\exception\ApiException
     */
    public function check($userInfo)
    {
        if (!$userInfo || !isset($userInfo['role_id']) || intval($userInfo['role_id']) < 0) {
            return false;
        }
        //获取权限表
        $actions = $this->getPermissionByRole($userInfo['role_id']);
        //权限验证逻辑
        $route = Request::url();
    }

    public function getPermissionByRole($roleId)
    {
        $cacheKey = md5('user_role_' . $roleId);
        $actions = Cache::get($cacheKey);
        if (!$actions || $actions == null || !is_array($actions)) {
            return [];
        }
        return $actions;
    }
}
