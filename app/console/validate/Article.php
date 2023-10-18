<?php
namespace app\console\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'title' => 'require',
        'cover_image' => 'require',
        'content' => 'require'
    ];

    protected $message = [
        'title.require' => '请填写标题',
        'cover_image.require' => '请上传封面图片',
        'content.require' => '请填写内容'
    ];

    protected $scene = [
        'create' => ['title', 'cover_image', 'content'],
        'update' => ['title', 'cover_image', 'content']
    ];
}
