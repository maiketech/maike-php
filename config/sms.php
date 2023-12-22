<?php
// 短信配置
return [
    'default' => ['aliyun'], //默认网关

    'aliyun' => [
        'access_key_id' => 'LTAI5tSecrQnQGpxR2uo4xx7',
        'access_key_secret' => 'fQaPghmjMlxJStMTO1Mer0y0NBDYeR',
        'sign_name' => '翔丰商业广场',
        'template' => [
            'jftz' => [
                'title' => '缴费通知(催缴)',
                'code' => 'SMS_463664810',
                'content' => '尊敬的业主、商户！您在翔丰商业广场${wuye}，物业费等费用共有${money}元未缴清，请您速办理缴费手续，逾期将收取违约金；如有疑问请与我们联系${phone}，感谢您对我们工作的支持！',
            ],
            'sfyz' => [
                'title' => '身份验证验证码',
                'code' => 'SMS_189465486',
                'content' => '验证码${code}，您正在进行身份验证，打死不要告诉别人哦！',
            ]
        ]
    ],

    'qcloud' => [
        'sdk_app_id' => '', // 短信应用的 SDK APP ID
        'secret_id' => '', // SECRET ID
        'secret_key' => '', // SECRET KEY
        'sign_name' => '翔丰商业广场', // 短信签名
    ],
];
