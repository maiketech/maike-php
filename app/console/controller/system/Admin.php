<?php

namespace app\console\controller\system;

use maike\util\StrUtil;
use app\console\controller\Base;
use app\model\system\Admin as AdminModel;

class Admin extends Base
{
    protected $modelClass = '\\app\\model\\system\\Admin';

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
        if (isset($params['status']) && intval($params['status']) > -1) {
            $where[] = ['status', '=', $params['status']];
        }
        if (isset($params['role_id']) && intval($params['role_id']) > 0) {
            $where[] = ['role_id', '=', $params['role_id']];
        }
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['username|name|email|mobile|desc', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }

    /**
     * API返回分页列表数据
     */
    public function list()
    {
        $where = $this->buildListWhere();
        $sort = $this->buildListSort();
        $list = $this->model->getList($where, $sort, $this->pageSize)->hidden(['password']);
        return $this->success($list);
    }

    /**
     * 新增
     *
     * @return \think\response\Json
     */
    public function create()
    {
        $params = $this->params();
        //验证信息
        $this->validate($params, "admin.create");
        $res = $this->model->save($params);
        if ($res) {
            return $this->success(null, '新增成功');
        }
        return $this->error("新增失败");
    }

    /**
     * 更新
     *
     * @return \think\response\Json
     */
    public function update()
    {
        $params = $this->params();
        //验证信息
        $this->validate($params, "admin.update");
        if (empty($params['password'])) {
            unset($params['password']);
        }
        $info = $this->model->get($params['admin_id']);
        if ($info->save($params)) {
            return $this->success(null, '更新成功');
        }
        return $this->error("更新失败");
    }

    /**
     * 设置状态
     * @param string $admin_id
     * @param integer $data  更新数据
     */
    public function set_status($admin_id, $data)
    {
        if (empty($admin_id)) return $this->error("无效参数");
        !is_array($admin_id) && $admin_id = StrUtil::ToArray($admin_id);
        $status = isset($data['status']) && $data['status'] == 1 ? 1 : 0;
        $res = $this->model->where('admin_id', 'in', $admin_id)->update(['status' => $status]);
        if ($res) {
            return $this->success(null, '设置成功');
        } else {
            return $this->success('设置失败');
        }
    }

    /**
     * 修改密码
     */
    public function set_pass()
    {
        $params = $this->params();
        if (!isset($params['old_password']) || $params['old_password'] == '') {
            return $this->error('请输入原密码');
        }
        if (!isset($params['password']) || $params['password'] == '') {
            return $this->error('请输入新密码');
        }
        if ($params['old_password'] == $params['password']) {
            return $this->error('新密码不能与原密码相同');
        }
        $user = AdminModel::get($this->adminId);
        if (!password_verify($params['old_password'], $user['password'])) {
            return $this->error('原密码错误');
        }
        if ($user->save(['password' => $params['password']])) {
            return $this->success(null, '修改成功');
        }
        return $this->error('密码修改失败');
    }

    /**
     * 删除管理员账号
     * 
     * @param string|array $admin_id
     */
    public function delete($admin_id)
    {
        if (empty($admin_id)) return $this->error("无效参数");
        !is_array($admin_id) && $admin_id = StrUtil::ToArray($admin_id);
        $hasCount = AdminModel::getCount([['admin_id', 'in', $admin_id], ['is_super', '=', 1]]);
        if ($hasCount > 0) {
            return $this->error("超级管理员不能删除");
        }

        if (AdminModel::destroy($admin_id)) {
            return $this->success(null, '删除成功');
        } else {
            return $this->error("删除失败");
        }
    }
}
