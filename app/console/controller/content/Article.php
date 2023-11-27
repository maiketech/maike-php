<?php

namespace app\console\controller\content;

use maike\util\StrUtil;
use app\console\controller\Base;
use app\model\content\Article as ArticleModel;

class Article extends Base
{
    protected $modelClass = '\\app\\model\\content\\Article';

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
            $where[] = ['title', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }

    /**
     * 新增
     */
    public function create()
    {
        $params = $this->params();
        //验证信息
        $this->validate($params, "article.create");
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
        $this->validate($params, "article.update");
        $info = ArticleModel::get($params['article_id']);
        if ($info->save($params)) {
            return $this->success(null, '更新成功');
        }
        return $this->error("更新失败");
    }

    /**
     * 设置状态
     * @param string $article_id
     * @param integer $data  更新数据
     */
    public function set_status($article_id, $data)
    {
        if (empty($article_id) || !isset($data['status'])) return $this->error("无效参数");
        !is_array($article_id) && $article_id = StrUtil::ToArray($article_id);
        $status = $data['status'] == 1 ? 1 : 0;
        $res = $this->model->where('article_id', 'in', $article_id)->update(['status' => $status]);
        if ($res) {
            return $this->success(null, '设置成功');
        } else {
            return $this->success('设置失败');
        }
    }

    /**
     * 删除
     */
    public function delete($article_id)
    {
        if (empty($article_id)) return $this->error("无效参数");
        !is_array($article_id) && $article_id = StrUtil::ToArray($article_id);
        if (ArticleModel::destroy($article_id)) {
            return $this->success(null, '删除成功');
        } else {
            return $this->error("删除失败");
        }
    }
}
