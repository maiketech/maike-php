<?php

namespace app\console\controller\system;

use maike\util\StrUtil;
use app\console\controller\Base;
use app\model\system\Attachment as AttachmentModel;
use app\model\system\AttachmentGroup;

class Attachment extends Base
{
    protected $modelClass = '\\app\\model\\system\\Attachment';

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
        $where = [
            ['uploader_id', '=', $this->adminId],
            ['module', '=', 'console']
        ];
        if (isset($params['type']) && !empty($params['type'])) {
            $where[] = ['type', '=', $params['type']];
        }
        if (isset($params['group_id']) && intval($params['group_id']) > -1) {
            $where[] = ['group_id', '=', $params['group_id']];
        }
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['name', 'like', '%' . $params['keyword'] . '%'];
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
        return ["create_time" => "desc"];
    }

    /**
     * 返回指定ID的附件数据
     *
     * @return \think\response\Json
     */
    public function get_by_id($attach_ids = false)
    {
        $params = $this->params();
        if (!$attach_ids || empty($attach_ids)) return $this->success(null);
        if (!is_array($attach_ids)) $attach_ids = StrUtil::ToArray($attach_ids);
        $where = [
            ['uploader_id', '=', $this->adminId],
            ['attach_id', 'in', $attach_ids],
            ['module', '=', 'console']
        ];
        //排序
        $sort = $this->getListSort($params);
        $list = $this->model->getAll($where, $sort);
        return $this->success($list);
    }

    public function group_list($type = 'image')
    {
        $where = [
            ['creator_id', '=', $this->adminId],
            ['type', '=', $type],
            ['module', '=', 'console']
        ];
        $model = new AttachmentGroup();
        $list = $model->getAll($where);
        return $this->success($list);
    }

    public function group_create()
    {
        $params = $this->params();
        $params['creator_id'] = $this->adminId;
        $params['module'] = 'console';
        $res = AttachmentGroup::create($params);
        if ($res) {
            return $this->success(null, "新增成功");
        }
        return $this->error("新增分组失败");
    }

    public function group_update($group_id = 0)
    {
        $res = AttachmentGroup::get($group_id);
        if ($res->save($this->params())) {
            return $this->success(null, "编辑成功");
        }
        return $this->error("编辑分组失败");
    }

    public function group_delete($group_id = 0)
    {
        if (empty($group_id) || $group_id < 1) return $this->error("删除失败");
        $count = AttachmentModel::getCount([['group_id', '=', $group_id]]);
        if ($count > 0) {
            return $this->error("分组下面存在文件，不能删除");
        }
        $res = AttachmentGroup::batchDelete([['group_id', '=', $group_id]]);
        if ($res) {
            return $this->success(null, "删除成功");
        }
        return $this->error("删除失败");
    }
}
