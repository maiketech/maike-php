<?php
return [
    // API请求状态码
    'status_code' => [
        'success' => 10000,
        'need_login' => 20000,
        'access_denied' => 30000
    ],

    // 跨域header
    'header'    => [
        'Access-Control-Allow-Origin'       => '*',
        'Access-Control-Allow-Headers'      => 'M-Token,Content-Type',
        'Access-Control-Allow-Methods'      => 'GET,POST,PATCH,PUT,DELETE,OPTIONS,DELETE',
        'Access-Control-Max-Age'            =>  '1728000',
        'Access-Control-Allow-Credentials'  => 'true'
    ],
    // Token参数名
    'token_key' => 'M-Token'
];
