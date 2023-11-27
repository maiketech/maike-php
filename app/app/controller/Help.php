<?php

namespace app\wxapp\controller;

use app\model\content\Help as HelpModel;

class Help extends Base
{
    protected $modelClass = '\\app\\model\\content\\Help';

    public function index()
    {
        $data = [
            'helpMenuCloumn' => 2,
            'helpMenu' => [
                [
                    'icon' => baseUrl() . 'static/image/nav1.png',
                    'text' => '在线客服',
                    'desc' => '',
                    'url' => 'api://open_customer_service_chat',
                    'need_login' => 0
                ],
                [
                    'icon' => baseUrl() . 'static/image/nav2.png',
                    'text' => '电话客服',
                    'desc' => '',
                    'url' => 'api://make_phone_call/0762-3300101',
                    'need_login' => 0
                ]
            ],
            'list' => $this->model->getAll(false, "views DESC", 6)
        ];
        return $this->success($data);
    }

    public function detail($help_id = 0)
    {
        if (empty($help_id)) return $this->error("无效参数");
        $detail = HelpModel::get($help_id);
        if ($detail) {
            $detail = $detail->toArray();
            return $this->success($detail);
        }
        $data['emptyImage'] = baseUrl() . 'static/image/empty.png';
        $data['emptyText'] = '内容不存在';
        return $this->error("内容不存在", $data);
    }
}
