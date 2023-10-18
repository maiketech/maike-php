<?php
use maike\exception\ExceptionHandle;
use maike\core\Request;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
