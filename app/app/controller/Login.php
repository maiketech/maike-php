<?php

namespace app\wxapp\controller;

use app\model\system\Setting as SettingModel;
use app\service\user\User as UserService;

class Login extends Base
{
    public function index()
    {
        $data = [
            "allowCodeLogin" => SettingModel::getItem("mobile_code_login", false)
        ];
        return $this->success($data);
    }

    /**
     * 登录
     */
    public function login($type = 'password')
    {
        $params = $this->params();
        $service = new UserService;
        if ($type == 'wechat') {
            $data = $service->wechatLogin($params);
        } else {
            $data = $service->login($params);
        }
        if (!$data || empty($data)) {
            return $this->error($service->getError() ?: '登录失败');
        }
        $data = [
            'userInfo' => $data['userInfo'],
            'userId' => isset($data['userInfo']['user_id']) ? $data['userInfo']['user_id'] : 0,
            'token' => $data['token'],
            'tokenExpire' => 86400
        ];
        return $this->success($data, '登录成功');
    }

    public function send_mobile_code()
    {
        return $this->success(null, '发送成功');
    }
}
