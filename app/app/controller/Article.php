<?php

namespace app\wxapp\controller;

use app\model\content\Article as ArticleModel;
use app\model\content\ArticleCategory as CategoryModel;

class Article extends Base
{
    protected $modelClass = '\\app\\model\\content\\Article';

    public function category()
    {
        $data = (new CategoryModel)->getAll(false, "sort ASC");
        if ($data) {
            $data = array_merge([['category_name' => '推荐', 'category_id' => 0]], $data->toArray());
        }
        return $this->success($data);
    }

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
        if (isset($params['category_id']) && intval($params['category_id']) > 0) {
            $where[] = ['category_id', '=', $params['category_id']];
        }
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['title', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }

    /**
     * 获取详情
     * 
     * @param integer $article_id
     */
    public function detail($article_id = 0)
    {
        if (empty($article_id)) return $this->error("无效参数");
        $detail = ArticleModel::get($article_id);
        $data['emptyImage'] = baseUrl() . 'static/image/empty.png';
        $data['emptyText'] = '资讯内容已删除';
        if ($detail) {
            $detail->inc("views")->update();
            $data['article'] = $detail->toArray();
            return $this->success($data);
        }
        return $this->error("资讯内容已删除", $data);
    }
}
