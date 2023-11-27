<?php

namespace app\wxapp\controller;

use app\model\content\Article as ArticleModel;
use app\model\content\Notice as NoticeModel;

class Page extends Base
{
    public function index()
    {
        $articleList = (new ArticleModel)->getAll([['category_id', '=', 1]], "create_time desc", 10);
        $noticeList = (new NoticeModel)->getAll(false, "create_time desc", 10);
        $data = [
            'pageStyle' => [
                'logo_image' => '',
                'background_color' => '#F9F9F9',
                'background_image' => BaseUrl() . 'static/image/ubg.jpg',
                'header_background_color' => '#F9F9F9',
                'header_background_image' => BaseUrl() . 'static/image/ubg.jpg'
            ],
            'articleList' => $articleList,
            'noticeList' => $noticeList,
            'iconMenu' => [
                [
                    'icon' => BaseUrl() . 'static/image/nav5.png',
                    'text' => '物业缴费',
                    'desc' => '物业缴费',
                    'url' => '/pages/fee/index',
                    'need_login' => 1
                ],
                [
                    'icon' => BaseUrl() . 'static/image/nav4.png',
                    'text' => '报事报修',
                    'desc' => '事件上报、维修上报',
                    'url' => '/pages/event/submit',
                    'need_login' => 1
                ],
                [
                    'icon' => BaseUrl() . 'static/image/nav3.png',
                    'text' => '通知公告',
                    'desc' => '通知公告',
                    'url' => '/pages/notice/list',
                    'need_login' => 0
                ],
                [
                    'icon' => BaseUrl() . 'static/image/nav1.png',
                    'text' => '投诉/建议',
                    'desc' => '投诉/建议',
                    'url' => '/pages/feedback/submit',
                    'need_login' => 1
                ],
            ]
        ];
        return $this->success($data);
    }
}
