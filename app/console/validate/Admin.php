<?php

namespace app\console\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'captcha' => 'require|captcha',
        'name' => 'require',
        'mobile' => 'require',
        'role_id' => 'require|>:-2',
        'old_password' => 'require'
    ];

    protected $message = [
        'username.require' => '请填写用户名',
        'password.require' => '请填写密码',
        'captcha.require' => '请填写验证码',
        'captcha.captcha' => '验证码错误',
        'name.require' => '请填写用户姓名',
        'mobile.require' => '请填写手机号码',
        'role_id.require' => '请选择角色',
        'role_id.>:-2' => '请选择角色',
        'old_password.require' => '请输入旧密码'
    ];

    protected $scene = [
        'login' => ['username', 'password', 'captcha'],
        'wxapp_login' => ['username', 'password'],
        'create' => ['mobile', 'password', 'name', 'role_id'],
        'update' => ['mobile', 'name', 'role_id'],
        'set_password' => ['old_password', 'password']
    ];
}
