<?php

namespace app\console\controller\system;

use app\console\controller\Base;
use app\model\system\Setting as SettingModel;

class Setting extends Base
{
    public function system()
    {
        if ($this->request->isPost()) {
            $saveData = [];
            $postData = $this->params(['group', 'values']);
            foreach ($postData['values'] as $key => $val) {
                $saveData[] = [
                    'where' => [['key', '=', $key]],
                    'data' => ["value" => $val]
                ];
            }
            $res = (new SettingModel)->batchUpdate($saveData);
            return $this->success($res);
        } else {
            $data = SettingModel::buildFormConfig("system");
            return $this->success($data);
        }
    }
}
