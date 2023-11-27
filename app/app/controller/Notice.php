<?php

namespace app\wxapp\controller;

use app\model\content\Notice as NoticeModel;

class Notice extends Base
{
    protected $modelClass = '\\app\\model\\content\\Notice';

    /**
     * 查询条件
     *
     * @param array $params
     * @return array
     */
    protected function buildListWhere($params = [])
    {
        //查询条件组装
        $where = [['status', '=', 1]];
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['title', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }

    /**
     * 获取详情
     * 
     * @param integer $Notice_id
     */
    public function detail($Notice_id = 0)
    {
        if (empty($Notice_id)) return $this->error("无效参数");
        $detail = NoticeModel::get($Notice_id);
        $data['emptyImage'] = baseUrl() . 'static/image/empty.png';
        $data['emptyText'] = '公告内容已删除';
        if ($detail) {
            $detail->inc("views")->update();
            $data['notice'] = $detail->toArray();
            return $this->success($data);
        }
        return $this->error("公告内容已删除", $data);
    }
}
