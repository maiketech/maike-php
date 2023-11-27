<?php

namespace app\console\controller\user;

use maike\util\StrUtil;
use app\console\controller\Base;
use app\model\user\User as UserModel;
use app\model\fee\OrderPay;

class User extends Base
{
    protected $modelClass = '\\app\\model\\user\\User';

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
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['name|mobile', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }


    /**
     * 新增
     *
     * @return \think\response\Json
     */
    public function create()
    {
        //验证信息
        $params = $this->params();
        //$this->validate($params, "customer.create");
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
        //验证信息
        $params = $this->params();
        //$this->validate($params, "customer.update");
        if (empty($params['password'])) {
            unset($params['password']);
        }
        $info = $this->model->get($params['user_id']);
        if ($info->save($params)) {
            return $this->success(null, '更新成功');
        }
        return $this->error("更新失败");
    }

    /**
     * 设置状态
     * @param string $user_id
     * @param integer $data  更新数据
     */
    public function set_status($user_id, $data)
    {
        if (empty($user_id)) return $this->error("无效参数");
        !is_array($user_id) && $user_id = StrUtil::ToArray($user_id);
        $status = isset($data['status']) && $data['status'] == 1 ? 1 : 0;
        $res = $this->model->where('user_id', 'in', $user_id)->update(['status' => $status]);
        if ($res) {
            return $this->success(null, '设置成功');
        } else {
            return $this->success('设置失败');
        }
    }

    public function set_pass($user_id)
    {
        if (empty($user_id)) return $this->error("无效参数");
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
        $customer = UserModel::get($user_id);
        if (!password_verify($params['old_password'], $customer['password'])) {
            return $this->error('原密码错误');
        }

        if ($customer->save(['password' => $params['password']])) {
            return $this->success(null, '修改成功');
        }
        return $this->error('密码修改失败');
    }

    /**
     * 删除
     *
     * @return \think\response\Json
     */
    public function delete($user_id)
    {
        if (empty($user_id)) return $this->error("无效参数");
        !is_array($user_id) && $user_id = StrUtil::ToArray($user_id);
        //是否产生业务数据，是则不能删除
        $hasCount = OrderPay::getCount([['user_id', 'in', $user_id]]);
        if ($hasCount > 0) {
            return $this->error("已产生业务数据，不能删除");
        }

        if (UserModel::destroy($user_id)) {
            return $this->success(null, '删除成功');
        } else {
            return $this->error("删除失败");
        }
    }
}
