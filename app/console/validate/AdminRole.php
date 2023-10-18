<?php

namespace app\console\validate;

use think\Validate;

class AdminRole extends Validate
{
    protected $rule = [
        'role_name' => 'require',
        'action_ids' => 'require'
    ];

    protected $message = [
        'role_name.require' => '请填写角色名称',
        'action_ids.require' => '请设置权限'
    ];

    protected $scene = [
        'create' => ['role_name', 'action_ids'],
        'update' => ['role_name', 'action_ids']
    ];
}
