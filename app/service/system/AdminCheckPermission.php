<?php

namespace maike\service\system;

use think\facade\Cache;
use think\facade\Request;
use maike\exception\ApiException;
use maike\service\BaseService;

class AdminCheckPermission extends BaseService
{
    /**
     * 验证权限
     * @param array $adminInfo
     * @return bool
     * @throws \maike\exception\ApiException
     */
    public function check($adminInfo)
    {
        if (!$adminInfo) {
            return false;
        }
        if (intval($adminInfo['is_super']) !== 1 && (!isset($adminInfo['role_id']) || intval($adminInfo['role_id']) < 0)) {
            return false;
        }
        //获取权限表
        $actions = $this->getPermissionByRole($adminInfo['role_id']);
        //权限验证逻辑
        $route = Request::url();
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
