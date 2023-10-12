<?php

return [
    // 默认磁盘
    'default' => 'local',
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => '/storage',
            // 可见性
            'visibility' => 'public',
            'validate' => [
                'video' => [
                    'video' => 'fileSize:' . (20 * 1024 * 1024) . '|fileExt:mp4,3gp,m3u8'
                ],
                'image' => [
                    'image' => 'fileSize:' . (5 * 1024 * 1024) . '|fileExt:jpg,jpeg,png'
                ],
                'file' => [
                    'file' => 'fileSize:' . (5 * 1024 * 1024) . '|fileExt:doc,xls,xlsx,ppt'
                ]
            ]
        ],
        // 更多的磁盘配置信息
    ],
];
