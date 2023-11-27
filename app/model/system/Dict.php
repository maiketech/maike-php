<?php

namespace app\model\system;

use app\model\BaseModel;
use maike\util\StrUtil;

class Dict extends BaseModel
{
    protected $append = ['dict_title'];

    //定义分组
    private static $dictTitleText = [
        'area' => '区域'
    ];

    public function getDictTitleAttr($value, $data)
    {
        return isset(self::$dictTitleText[$data['dict']]) ? self::$dictTitleText[$data['dict']] : '';
    }

    /**
     * 获取分组信息
     *
     * @return array
     */
    public static function getDictList()
    {
        return self::$dictTitleText;
    }

    /**
     * 根据分组获取数据字典数据
     *
     * @param string $dict  分组名
     * @return \think\Collection
     */
    public static function getByDict($dict = '')
    {
        $where = [];
        if (!empty($dict)) {
            $where[] = ['dict', '=', $dict];
        }
        $data = (new static)->where($where)->select();
        return $data && $data->count() > 0 ? $data->toArray() : null;
    }

    /**
     * 根据分组及字典值返回字典描述数组
     *
     * @param string $dict  分组名
     * @param array $values  字典值
     * @return array
     */
    public static function getTitleByValue($dict = '', $values = [])
    {
        if (!is_array($values)) {
            $values = StrUtil::ToArray($values);
        }
        $res = self::getByDict($dict);
        $arr = [];
        foreach ($res as $item) {
            if (in_array((string)$item['value'], $values)) {
                $arr[] = $item['title'];
            }
        }
        return count($arr) > 0 ? $arr : [];
    }
}
