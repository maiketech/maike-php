<?php

namespace app\console\controller\content;

use app\console\controller\Base;
use app\model\content\Article as ArticleModel;
use app\model\content\ArticleCategory as CategoryModel;
use maike\util\StrUtil;

class ArticleCategory extends Base
{
    protected $modelClass = '\\app\\model\\content\\ArticleCategory';

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
            $where[] = ['category_name', 'like', '%' . $params['keyword'] . '%'];
        }
        return $where;
    }

    public function get_options($limit = 100)
    {
        $list = CategoryModel::getAllByCache();
        $data = [];
        foreach ($list as $item) {
            $data[] = [
                'label' => $item['category_name'],
                'value' => $item['category_id']
            ];
        }
        return $this->success($data);
    }

    /**
     * 新增
     */
    public function create()
    {
        $params = $this->params();
        //验证信息
        $this->validate($params, "article_category.create");
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
        $params = $this->params();
        //验证信息
        $this->validate($params, "article_category.update");
        $info = $this->model->get($params['category_id']);
        if ($info->save($params)) {
            return $this->success(null, '更新成功');
        }
        return $this->error("更新失败");
    }

    /**
     * 设置状态
     * @param string $category_id
     * @param integer $data  更新数据
     */
    public function set_status($category_id, $data = [])
    {
        if (empty($category_id) || !isset($data['status'])) return $this->error("无效参数");
        !is_array($category_id) && $category_id = StrUtil::ToArray($category_id);
        $status = $data['status'] == 1 ? 1 : 0;
        $res = $this->model->where('category_id', 'in', $category_id)->update(['status' => $status]);
        if ($res) {
            return $this->success(null, '设置成功');
        } else {
            return $this->success('设置失败');
        }
    }

    /**
     * 删除
     *
     * @return \think\response\Json
     */
    public function delete($category_id)
    {
        if (empty($category_id)) return $this->error("无效参数");
        !is_array($category_id) && $category_id = StrUtil::ToArray($category_id);
        // 判断是否已使用
        $hasArticle = ArticleModel::getCount([['category_id', 'in', $category_id]]);
        if ($hasArticle > 0) {
            return $this->error("分类下存在文章，删除文章后才可删除此分类");
        }
        // 删除
        if (CategoryModel::destroy($category_id)) {
            return $this->success(null, '删除成功');
        } else {
            return $this->error("删除失败");
        }
    }
}
