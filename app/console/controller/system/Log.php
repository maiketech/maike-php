<?php

namespace app\console\controller\system;

use app\console\controller\Base;

class Log extends Base
{
    protected $modelClass = '\\app\\model\\system\\SystemLog';

    /**
     * 查询条件
     *
     * @param array $params
     * @return array
     */
    protected function buildListWhere($params = [])
    {
        if (!$params || empty($params)) $params = $this->params();
        //查询条件组装
        $where = [];
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['action|content|username', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }
}
