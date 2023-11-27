<?php

namespace app\console\validate;

use think\Validate;

class Notice extends Validate
{
    protected $rule = [
        'title' => 'require',
        'content' => 'require'
    ];

    protected $message = [
        'title.require' => '请填写标题',
        'content.require' => '请填写内容'
    ];

    protected $scene = [
        'create' => ['title', 'content'],
        'update' => ['title', 'content']
    ];
}
