<?php

namespace app\console\controller\system;

use app\console\controller\Base;
use app\model\system\Dict as DictModel;
use maike\util\StrUtil;

class Dict extends Base
{
    protected $modelClass = '\\app\\model\\system\\Dict';

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
        if (isset($params['dict']) && !empty($params['dict'])) {
            $where[] = ['dict', '=', $params['dict']];
        }
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['title|key|value', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }

    /**
     * 排序条件
     *
     * @param array $params
     * @return array|string
     */
    protected function getListSort($params = [])
    {
        if (!$params || empty($params)) $params = $this->params();
        $sorter = isset($params['sorter']) && isset($params['sorter']['field']) ? $params['sorter'] : null;
        if ($sorter && $sorter !== null) {
            return [str_replace("_desc", "", $sorter['field']) => str_replace("end", "", $sorter['order'])];
        }
        return ["dict" => "asc", 'id' => 'desc'];
    }

    public function get_item_options($dict = '')
    {
        if (!$dict || empty($dict)) return $this->success();
        $data = DictModel::getByDict($dict);
        foreach ($data as &$item) {
            $item['label'] = $item['title'];
        }
        return $this->success($data);
    }

    public function get_options($limit = 100)
    {
        $list = DictModel::getDictList();
        $data = [];
        foreach ($list as $key => $value) {
            $data[] = [
                'label' => $value,
                'value' => $key
            ];
        }
        return $this->success($data);
    }

    /**
     * 新增
     */
    public function create()
    {
        //验证信息
        $params = $this->params();
        //$this->validate($params, "dict.create");
        $res = $this->model->save($params);
        if ($res) {
            return $this->success(null, '新增成功');
        }
        return $this->error("新增失败");
    }

    /**
     * 更新
     */
    public function update()
    {
        //验证信息
        $params = $this->params();
        //$this->validate($params, "dict.update");
        $info = $this->model->get($params['key']);
        if ($info->save($params)) {
            return $this->success(null, '更新成功');
        }
        return $this->error("更新失败");
    }

    /**
     * 删除
     */
    public function delete($key)
    {
        if (empty($key)) return $this->error("无效参数");
        !is_array($key) && $key = StrUtil::ToArray($key);
        //if (!DictModel::isAllowDelete($id)) return $this->error("已有关联数据，不能删除");
        if (DictModel::destroy($key)) {
            return $this->success(null, '删除成功');
        } else {
            return $this->error("删除失败");
        }
    }
}
