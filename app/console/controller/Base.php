<?php

namespace app\console\controller;

use maike\core\Controller;

class Base extends Controller
{
    // 模型
    protected $modelClass = '';
    protected $model = null;

    // 分页大小
    protected $pageSize = 10;

    // 管理员用户信息
    protected $token;
    protected $adminId;
    protected $adminInfo;

    /**
     * 初始化
     */
    public function initialize()
    {
        $this->token = $this->request->header("M-Token");
        // 登录的用户信息
        $this->adminId = $this->request->hasMacro("adminId") ? $this->request->adminId() : 0;
        $this->adminInfo = $this->request->hasMacro("adminInfo") ? $this->request->adminInfo() : null;
        // 初始化分页大小
        $this->pageSize = intval($this->params('pageSize', 10));
        // 加载模型
        $this->initModel();
        $this->init();
    }

    protected function init()
    {
    }

    protected function initModel()
    {
        if ($this->modelClass && !empty($this->modelClass)) {
            $this->model = new $this->modelClass();
        }
    }

    /**
     * 查询条件
     *
     * @param array $params
     * @return array
     */
    protected function buildListWhere($params = null)
    {
        if (!$params || empty($params)) $params = $this->params();
        return [];
    }

    /**
     * 排序条件
     *
     * @param array $params
     * @return array|string
     */
    protected function buildListSort($params = null)
    {
        if (!$params || empty($params)) $params = $this->params();
        $sorter = isset($params['sorter']) && isset($params['sorter']['field']) ? $params['sorter'] : null;
        if ($sorter && $sorter !== null) {
            return [str_replace("_desc", "", $sorter['field']) => str_replace("end", "", $sorter['order'])];
        }
        if ($this->model && !empty($this->model->pk)) {
            return [$this->model->pk => "desc"];
        }
        return ["create_time" => "desc"];
    }

    /**
     * API返回分页列表数据
     */
    public function list()
    {
        //查询条件
        $where = $this->buildListWhere();
        //排序
        $sort = $this->buildListSort();

        $list = $this->model->getList($where, $sort, $this->pageSize);
        return $this->success($list);
    }

    /**
     * 获取详情
     */
    public function detail()
    {
        $pk = $this->model->pk;
        $id = $this->params([
            $pk => 0
        ]);
        if (empty($id) && intval($id) < 1) return $this->error("无效参数");
        $info = $this->model::get($id);
        return $this->success($info);
    }

    /**
     * 表单选项数据
     *
     * @param integer $limit
     */
    public function get_options($limit = 100)
    {
        $where = $where = $this->buildListWhere();
        $sort = $this->buildListSort();
        $data = $this->model->getAll($where, $sort, $limit);
        return $this->success($data);
    }
}
