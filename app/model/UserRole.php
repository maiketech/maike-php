<?php

namespace app\model;

use maike\utils\Arr;
use maike\utils\Str;

class UserRole extends BaseModel
{
    protected $pk = 'role_id';
    protected $append = ['status_desc', 'not_allow_delete'];

    public function setActionIdsAttr($value)
    {
        return is_array($value) ? Arr::ToString($value) : $value;
    }

    public function getActionIdsAttr($value)
    {
        return !empty($value) ? Str::ToArray($value, ",", "int") : [];
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
