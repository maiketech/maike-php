<?php

namespace app\model\system;

use app\model\BaseModel;
use maike\util\StrUtil;

class Dict extends BaseModel
{
    protected $append = ['group_desc'];

    //定义分组
    private static $groupText = [
        'group' => '标签'
    ];

    public function getGroupDescAttr($value, $data)
    {
        return isset(self::$groupText[$data['group']]) ? self::$groupText[$data['group']] : '';
    }

    /**
     * 获取分组信息
     *
     * @return array
     */
    public static function getGroupList()
    {
        return self::$groupText;
    }

    /**
     * 根据分组获取数据字典数据
     *
     * @param string $group  分组名
     * @return \think\Collection
     */
    public static function getByGroup($group = '')
    {
        $where = [];
        if (!empty($group)) {
            $where[] = ['group', '=', $group];
        }
        $data = (new static)->where($where)->select();
        return $data && $data->count() > 0 ? $data->toArray() : null;
    }

    /**
     * 根据分组及字典值返回字典描述数组
     *
     * @param string $group  分组名
     * @param array $values  字典值
     * @return array
     */
    public static function getTitleByGroup($group = '', $values = [])
    {
        if (!is_array($values)) {
            $values = StrUtil::ToArray($values);
        }
        $res = self::getByGroup($group);
        $arr = [];
        foreach ($res as $item) {
            if (in_array((string)$item['value'], $values)) {
                $arr[] = $item['title'];
            }
        }
        return count($arr) > 0 ? $arr : ['无'];
    }
}
