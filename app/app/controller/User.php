<?php

namespace app\wxapp\controller;

use app\service\system\Upload as UploadService;

class User extends Base
{
    public function index()
    {
        $userInfo = $this->getCurUser();
        $data = [
            //页面样式
            'pageStyle' => [
                'background_color_top' => '#0049E2', //下拉刷新顶部背景色
                'background_color' => '#FFFFFF', //
                'background_image' => BaseUrl() . 'static/image/ubg.jpg',
            ],
            'userId' => $userInfo ? $userInfo['user_id'] : 0,
            'userInfo' => $userInfo,
            'menuData' => $this->getMenuData()
        ];
        return $this->success($data);
    }

    public function set_avatar()
    {
        $upService = new UploadService();
        $files = $upService->image($this->userId);
        if ($files && $files->count() > 0) {
            $files = $files->toArray();
            if ($this->userInfo->save(['avatar' => $files[0]['file_url']])) {
                return $this->success();
            }
        }
        return $this->error("设置失败");
    }

    public function set_pass()
    {
        $params = $this->params();
        if (!isset($params['old_password']) || $params['old_password'] == '') {
            return $this->error('请输入原密码');
        }
        if (!isset($params['password']) || $params['password'] == '') {
            return $this->error('请输入新密码');
        }
        if ($params['old_password'] == $params['password']) {
            return $this->error('新密码不能与原密码相同');
        }
        if (!password_verify($params['old_password'], $this->userInfo['password'])) {
            return $this->error('原密码错误');
        }
        if ($this->userInfo->save(['password' => $params['password']])) {
            return $this->success(null, '修改成功');
        }
        return $this->error('密码修改失败');
    }

    private function getMenuData()
    {
        //url_type：page页面、app小程序、web网页、open开放接口，modal弹框
        $menuData = [
            [
                'icon' => baseUrl() . 'static/image/um4.png',
                'text' => '报事报修记录',
                'url' => '/pages/event/my',
                'need_login' => 1,
                'desc' => '',
            ], [
                'icon' => baseUrl() . 'static/image/um4.png',
                'text' => '投诉/建议记录',
                'url' => '/pages/feedback/my',
                'need_login' => 1,
                'desc' => '',
            ], [
                'icon' => baseUrl() . 'static/image/um4.png',
                'text' => '商铺绑定',
                'url' => '/pages/wuye/list',
                'need_login' => 1,
                'desc' => '',
            ], [
                'icon' => baseUrl() . 'static/image/um3.png',
                'text' => '客服、常见问题',
                'url' => '/pages/help/index',
                'need_login' => 0,
                'desc' => '',
            ], [
                'icon' => baseUrl() . 'static/image/um5.png',
                'text' => '设置',
                'url' => '/pages/setting/index',
                'need_login' => 0,
                'desc' => '',
            ]
        ];
        return $menuData;
    }
}
