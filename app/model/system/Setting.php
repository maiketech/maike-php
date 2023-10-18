<?php

namespace app\model\system;

use app\model\BaseModel;
use maike\util\Str;
use maike\util\Arr;

class Setting extends BaseModel
{
    protected $pk = 'key';
    protected $json = ['options'];

    public static $group = [
        'system' => '基础设置'
    ];

    public function getValueAttr($value, $data)
    {
        if (isset($data['input_type'])) {
            if ($data['input_type'] == 'event_type_select') {
                $options = (array)$data['options'];
                if (isset($options['mode']) && $options['mode'] == 'multiple') {
                    return $data['value'] == null ? [] : Str::ToArray($data['value'], ",", "int");
                }
            } else if ($data['input_type'] == 'editor') {
                return empty($value) ? "" : htmlspecialchars_decode($value);
            }
        }
        return $value;
    }

    public function setValueAttr($value, $data)
    {
        if (!empty($value)) {
            return is_array($value) ? Arr::ToString($value) : $value;
        }
        return $value;
    }

    /**
     * 获取指定项设置
     * @param $key
     * @return array
     */
    public static function getItem($key, $defalutValue = null)
    {
        $data = self::getAllByCache();
        if ($data) {
            $data = Arr::Sort($data, 'sort');
            $data = Arr::ColumnToKey($data, "key");
        }
        if (strpos($key, ".") !== false) {
            $arr = Str::ToArray($key, ".");
            if (count($arr) > 1) {
                $group = $arr[0];
                $item = $arr[1];
                $itemData = false;
                if (!empty($group) && !empty($item)) {
                    $groupData = Arr::Search($data, 'group', $group);
                    if ($groupData) $itemData = Arr::Search($data, 'key', $item);
                } else if (!empty($item)) {
                    $itemData = Arr::Search($data, 'key', $item);
                }
                return $itemData && isset($itemData['value']) ? $itemData['value'] : $defalutValue;
            }
        }
        return isset($data[$key]) ? $data[$key]['value'] : $defalutValue;
    }

    /**
     * 获取指定分组的设置
     * @param $group
     * @return array
     */
    public static function getItemByGroup($group)
    {
        $data = self::getAllByCache();
        if ($data) {
            $data = Arr::Sort($data, 'sort');
            $data = Arr::Search($data, 'group', $group);
        }
        $setting = [];
        foreach ($data as $item) {
            $setting[$item['key']] = $item['value'];
        }
        return $setting;
    }

    //生成表单配置数据
    public static function buildFormConfig()
    {
        $data = self::getAllByCache(-1);
        if ($data) {
            $data = Arr::Sort($data, 'sort');
        }
        $itemArr = [];
        foreach (self::$group as $g => $title) {
            $tmp = Arr::Search($data, 'group', $g);
            $items = [];
            foreach ($tmp as $item) {
                $items[] = [[
                    "label" => $item['title'],
                    "name"  => $item['key'],
                    "type" => $item['input_type'],
                    "options" => $item['options'],
                    "value" => $item['value'],
                    "help" => $item['help']
                ]];
            }

            $itemArr[] = [
                'title' => $title,
                'items' => $items
            ];
        }
        return $itemArr;
    }
}
