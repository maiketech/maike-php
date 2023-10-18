<?php

namespace app\model\system;

use app\model\BaseModel;

/**
 * 上传文件库模型
 */
class Attachment extends BaseModel
{
    // 定义主键
    protected $pk = 'attach_id';
    protected $append = ['url', 'thumb'];
    protected $with = ['group'];

    public function setPathAttr($value)
    {
        return str_replace("\\", "/", $value);
    }

    public function getThumbAttr($value, $data)
    {
        if ($data['storage'] === "local" || $data['storage'] === "public") {
            $data['domain'] = rtrim(BaseUrl()) . "uploads";
        }
        $url = '';
        if (isset($data['type']) && ($data['type'] == 'video' || $data['type'] == 'file')) {
            //video、file
            if ($value && !empty($value)) {
                $url = "{$data['domain']}/{$value}";
            } else {
                if ($data['type'] == 'file') {
                    $url = rtrim(BaseUrl()) . "static/image/default_file.jpg";
                } else {
                    $url = rtrim(BaseUrl()) . "static/image/default_video.jpg";
                }
            }
        } else {
            //image
            if ($value && !empty($value)) {
                $url = "{$data['domain']}/{$value}";
            } else {
                $url = "{$data['domain']}/{$data['path']}";
            }
        }
        return $url;
    }

    /**
     * 文件URL
     * @param $value
     * @param $data
     * @return string
     */
    public function getUrlAttr($value, $data)
    {
        if ($data['storage'] === "local" || $data['storage'] === "public") {
            $data['domain'] = rtrim(BaseUrl()) . "uploads";
        }
        return "{$data['domain']}/{$data['path']}";
    }

    /**
     * 关联文件库分组表
     * @return \think\model\relation\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(AttachmentGroup::class, 'group_id');
    }
}
