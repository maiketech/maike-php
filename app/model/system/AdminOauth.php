<?php

namespace app\model\system;

use think\model\concern\SoftDelete;
use app\model\BaseModel;

class AdminOauth extends BaseModel
{
    use SoftDelete;
    
    /**
     * 根据OauthID获取OAuth信息
     * @param string $OauthID
     * @return \think\Collection
     */
    public static function getByOauthId(string $OauthID)
    {
        $where = [
            ['oauth_id', '=', $OauthID],
        ];
        return (new static)->where($where)->find();
    }

    /**
     * 根据OauthID(openid,userid)获取user_id
     * @param string $OauthID
     * @return int
     */
    public static function getUserIdByOauthId(string $OauthID): int
    {
        $where = [
            ['oauth_id', '=', $OauthID],
        ];
        $id = (new static)->where($where)->value("user_id");
        return !$id || empty($id) ? 0 : $id;
    }

    /**
     * 根据unionid获取user_id
     * @param string $unionId
     * @return integer
     */
    public static function getUserIdByUnionId(string $unionId): int
    {
        $where = [
            ['unionid', '=', $unionId],
        ];
        $id = (new static)->where($where)->value("user_id");
        return !$id || empty($id) ? 0 : $id;
    }
}
