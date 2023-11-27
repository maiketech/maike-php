<?php

namespace app\console\validate;

use think\Validate;

class ArticleCategory extends Validate
{
    protected $rule = [
        'category_name' => 'require'
    ];

    protected $message = [
        'category_name.require' => '请填写分类名称'
    ];

    protected $scene = [
        'create' => ['category_name'],
        'update' => ['category_name']
    ];
}
