<?php

namespace app\model\content;

use app\model\BaseModel;

class Help extends BaseModel
{
    protected $pk = 'help_id';
    protected $append = ['status_desc'];

    public function getStatusDescAttr($value, $data)
    {
        return isset($data['status']) && $data['status'] == 1 ? '已发布' : '未发布';
    }

    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
}
