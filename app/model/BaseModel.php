<?php

namespace app\model;

use think\facade\Cache;
use maike\core\Model;

class BaseModel extends Model
{
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    /**
     * 从缓存中获取数据
     * @param array|null $where 查询条件
     * @param int $expireTime 过期时间（秒）, 0为不过期, -1重新查询强制更新缓存
     * @return array|mixed
     */
    public static function getAllByCache(int $expireTime = 0)
    {
        $model = new static;
        $cacheKey = $model->name . '__all_data';
        $data = null;
        if ($expireTime === -1 || !$data = Cache::get($cacheKey)) {
            $data = $model->getAll();
            if ($data) {
                $data = $data->toArray();
            }
            if ($expireTime === -1) $expireTime = 0;
            Cache::set($cacheKey, $data, $expireTime);
        }
        return $data;
    }

    public function deleteAllCache()
    {
        $cacheKey = $this->name . '__all_data';
        \think\facade\Log::write($cacheKey, "debug");
        return Cache::delete($cacheKey);
    }

    public static function onAfterWrite($model)
    {
        $model->deleteAllCache();
    }

    public static function onAfterDelete($model)
    {
        $model->deleteAllCache();
    }
}
