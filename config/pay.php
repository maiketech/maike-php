<?php
//支付配置
return [
    'wechat' => [
        'mch_id' => '1650549267',
        'mch_secret_key_v2' => '1pPIogGNfCyzeqtX8Vcd6n4bYuTFURkE', //APIv2
        'mch_secret_key' => 'M3aBZqLz6O4WVymvYCh1N0JU5DeGtbx7', //APIv3
        'mch_cert_key' => app()->getRootPath() . 'data/certs/wechat_pay_key.pem',
        'mch_cert' => app()->getRootPath() . 'data/certs/wechat_pay_cert.pem',
        'mch_cert_serial' => '', // 商户证书序列号
        'notify_url' => 'https://123.com/wechat/notify',
        'mp_app_id' => '',
        'mini_app_id' => 'wxf9f8cee6565ddfd3',
        // 选填-微信平台公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
        //'wechat_cert_serial' => '4158D532C738E396DB6B434558FA6DDA1C98687E',
    ]
];
