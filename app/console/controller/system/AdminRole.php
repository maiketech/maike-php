<?php

namespace app\console\controller\system;

use maike\util\StrUtil;
use maike\util\ArrUtil;
use app\console\controller\Base;
use app\model\system\Admin as AdminModel;
use app\model\system\AdminRole as RoleModel;
use app\model\system\Action as ActionModel;

class AdminRole extends Base
{
    protected $modelClass = '\\app\\model\\system\\AdminRole';

    /**
     * 生成查询条件
     *
     * @param array $params
     * @return array
     */
    protected function buildListWhere($params = null)
    {
        if (!$params || empty($params)) $params = $this->params();
        //查询条件组装
        $where = [];
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['role_name|desc', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }

    /**
     * 表单选项数据
     */
    public function get_options($limit = 100)
    {
        $list = RoleModel::getAllByCache();
        $data = [];
        foreach ($list as $item) {
            $data[] = [
                'label' => $item['role_name'],
                'value' => $item['role_id']
            ];
        }
        return $this->success($data);
    }

    private function buildSaveData($params = null)
    {
        if (!$params || empty($params)) {
            $params = $this->params();
        }
        if (isset($params['action_ids']) && is_array($params['action_ids']) && count($params['action_ids']) > 0) {
            $temp = ActionModel::getById($params['action_ids']);
            $params['auths'] = ArrUtil::Column($temp, "auth");
            $params['actions'] = ArrUtil::Column($temp, "action");
        }
        return $params;
    }

    /**
     * 新增
     */
    public function create()
    {
        $params = $this->buildSaveData();
        //验证信息
        $this->validate($params, "admin_role.create");
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
        $params = $this->buildSaveData();
        //验证信息
        $this->validate($params, "admin_role.update");
        $info = $this->model->get($params['role_id']);
        if ($info->save($params)) {
            return $this->success(null, '更新成功');
        }
        return $this->error("更新失败");
    }

    /**
     * 设置状态
     * @param string|array $role_id
     * @param array $data  更新数据
     */
    public function set_status($role_id, $data = [])
    {
        if (empty($role_id) || !isset($data['status'])) return $this->error("无效参数");
        !is_array($role_id) && $role_id = StrUtil::ToArray($role_id);
        $status = $data['status'] == 1 ? 1 : 0;
        $res = $this->model->where('role_id', 'in', $role_id)->update(['status' => $status]);
        if ($res) {
            return $this->success(null, '设置成功');
        } else {
            return $this->success('设置失败');
        }
    }

    /**
     * 删除
     */
    public function delete($role_id)
    {
        if (empty($role_id)) return $this->error("无效参数");
        !is_array($role_id) && $role_id = StrUtil::ToArray($role_id);
        //判断是否有使用
        $hasAdmin = AdminModel::getCount([['role_id', 'in', $role_id]]);
        if ($hasAdmin > 0) {
            return $this->error("已有管理员账号使用此角色，不能删除");
        }
        // 删除角色
        if (RoleModel::destroy($role_id)) {
            return $this->success(null, '删除成功');
        } else {
            return $this->error("删除失败");
        }
    }
}
