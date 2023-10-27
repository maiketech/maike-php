<?php

namespace app\model\content;

use app\model\BaseModel;

class Notice extends BaseModel
{
    protected $pk = 'notice_id';
    protected $append = ['status_desc'];

    public function getStatusDescAttr($value, $data)
    {
        return isset($data['status']) && $data['status'] == 1 ? '已发布' : '未发布';
    }

    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * 获取最新一条公告
     * @return \think\Collection
     */
    public static function getLastOne()
    {
        return (new static)->order("create_time DESC")->find();
    }

    /**
     * 获取今天最新一条公告
     * @return \think\Collection
     */
    public static function getTodayOne()
    {
        return (new static)->whereDay("create_time")->order("create_time DESC")->find();
    }
}
