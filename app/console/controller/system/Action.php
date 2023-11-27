<?php

namespace app\console\controller\system;

use maike\util\ArrUtil;
use app\console\controller\Base;
use app\model\system\Action as ActionModel;

class Action extends Base
{
    /**
     * 表单选项数据
     */
    public function get_options($limit = 100)
    {
        $list = ActionModel::getAllByCache();
        $data = [];
        if ($list) {
            $data = ArrUtil::ToTree($list, -1, "action_id");
            foreach ($data as &$item) {
                $item['key'] = $item['action_id'];
                $item['value'] = $item['action_id'];
                if (isset($item['children'])) {
                    foreach ($item['children'] as &$sub) {
                        $sub['key'] = $sub['action_id'];
                        $sub['value'] = $sub['action_id'];
                        if (isset($sub['children'])) {
                            foreach ($sub['children'] as &$t) {
                                $t['key'] = $t['action_id'];
                                $t['value'] = $t['action_id'];
                            }
                        }
                    }
                }
            }
        }
        return $this->success($data);
    }
}
