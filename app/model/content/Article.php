<?php

namespace app\model\content;

use think\model\concern\SoftDelete;
use maike\util\DateTimeUtil;
use maike\util\StrUtil;
use app\model\BaseModel;

class Article extends BaseModel
{
    use SoftDelete;

    protected $pk = 'article_id';
    protected $with = ['category'];
    protected $append = ['status_text', 'time_text', 'intro'];

    public function getStatusTextAttr($value, $data)
    {
        return isset($data['status']) && $data['status'] == 1 ? '已发布' : '未发布';
    }

    public function getTimeTextAttr($value, $data)
    {
        return isset($data['create_time']) && $data['create_time'] > 0 ? DateTimeUtil::Format($data['create_time'], 'Y-m-d') : '--';
    }

    public function getIntroAttr($value, $data)
    {
        return isset($data['content']) && !empty($data['content']) ? StrUtil::Sub(htmlspecialchars_decode($data['content']), 36) : '';
    }

    /**
     * 文章详情：HTML实体转换回普通字符
     * @param $value
     * @return string
     */
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * 获取置顶文章
     *
     * @return array
     */
    public static function getTop($limit = 10, $categoryId = 0)
    {
        $where = [
            ['status', '=', 1],
            ['is_top', '=', 1]
        ];
        if ($categoryId > 0) {
            $where[] = ['category_id', '=', $categoryId];
        }
        $data = (new static)->getAll($where, "views DESC,sort ASC", $limit);
        if ($data && !$data->isEmpty()) {
            return $data->toArray();
        }
        return null;
    }

    /**
     * 获取热门文章
     *
     * @return array
     */
    public static function getHot($limit = 10, $categoryId = 0)
    {
        $where = [
            ['status', '=', 1]
        ];
        if ($categoryId > 0) {
            $where[] = ['category_id', '=', $categoryId];
        }
        $data = (new static)->getAll($where, "views DESC,sort ASC", $limit);
        if ($data && !$data->isEmpty()) {
            return $data->toArray();
        }
        return null;
    }

    public function category()
    {
        return $this->hasOne('ArticleCategory', 'category_id', 'category_id')->bind(['category_name']);
    }
}
