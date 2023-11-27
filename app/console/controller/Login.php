<?php

namespace app\console\controller;

use app\service\system\Admin as AdminService;
use maike\payment\Pay as PayService;

class Login extends Base
{
    public function login()
    {
//         $order = [
//             'out_trade_no' => '34677888339908982',
//             'amount' => 0.01,
//             'openid' => 'oiMmT60unguLydXmkkeB-HmCq69Q'
//         ];
//         $payService = PayService::Wechat();
//                 $payData = $payService->create($order);
// halt($payData,$payService->getError());

        $params = $this->params();
        $this->validate($params, "admin.login");
        $model = new AdminService;
        $data = $model->login($params['username'], $params['password']);
        if (!$data || empty($data)) {
            return $this->error($model->getError() ?: '登录失败');
        }

        $data = [
            'adminInfo' => $data['info'],
            'token' => $data['token'],
            'tokenExpire' => 86400, // 24 hours
            'menu' => $model->getMenuData($data['info']),
            'authList' => $data['info']['auths']
        ];
        return $this->success($data, '登录成功');
    }
}
