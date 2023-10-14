<?php

namespace app\model;

use maike\utils\DT;
use maike\utils\Str;

class Article extends BaseModel
{
    protected $pk = 'article_id';
    protected $with = ['category'];
    protected $append = ['status_text', 'time_text', 'intro'];

    public function getStatusTextAttr($value, $data)
    {
        return isset($data['status']) && $data['status'] == 1 ? '已发布' : '未发布';
    }

    public function getTimeTextAttr($value, $data)
    {
        return isset($data['create_time']) && $data['create_time'] > 0 ? DT::Format($data['create_time'], 'Y-m-d') : '--';
    }

    public function getIntroAttr($value, $data)
    {
        return isset($data['content']) && !empty($data['content']) ? Str::Sub($data['content'], 30) : '';
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
     * 获取置顶（小程序首页）
     *
     * @return array
     */
    public static function getTop($limit = 10)
    {
        $where = [['status', '=', 1]];
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
