<?php

namespace app\model\system;

use app\model\BaseModel;
use maike\util\ArrUtil;
use maike\util\StrUtil;

class AdminRole extends BaseModel
{
    protected $pk = 'role_id';
    protected $append = ['status_desc', 'not_allow_delete'];

    public function setActionIdsAttr($value)
    {
        return is_array($value) ? ArrUtil::ToString($value) : $value;
    }

    public function getActionIdsAttr($value)
    {
        return !empty($value) ? StrUtil::ToArray($value, ",", "int") : [];
    }

    //获取字段处理
    public function getStatusDescAttr($value, $data)
    {
        return isset($data['status']) && $data['status'] == 1 ? '正常' : '禁用';
    }

    public function getNotAllowDeleteAttr($value, $data)
    {
        return isset($data['is_system']) && $data['is_system'] == 1;
    }
}
