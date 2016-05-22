<?php

define('BASE_URL', 'http://admin.shop.com');
return array(
    //'配置项'=>'配置值'
    'URL_MODEL'         => 2,
    'TMPL_PARSE_STRING' => [
        '__CSS__'       => BASE_URL . '/Public/css',
        '__JS__'        => BASE_URL . '/Public/js',
        '__IMG__'       => BASE_URL . '/Public/images',
        '__UPLOADIFY__' => BASE_URL . '/Public/ext/uploadify',
        '__LAYER__'     => BASE_URL . '/Public/ext/layer',
        '__ZTREE__'     => BASE_URL . '/Public/ext/ztree',
        '__TREE_GRID__'     => BASE_URL . '/Public/ext/treegrid',
        '__UE__'     => BASE_URL . '/Public/ext/ue',
        '__ROOT__'     => BASE_URL,
    ],
    /* 数据库设置 */
    'DB_TYPE'           => 'mysql', // 数据库类型
    'DB_HOST'           => '127.0.0.1', // 服务器地址
    'DB_NAME'           => 'tp1229', // 数据库名
    'DB_USER'           => 'root', // 用户名
    'DB_PWD'            => '123456', // 密码
    'DB_PORT'           => '3306', // 端口
    'DB_PREFIX'         => '', // 数据库表前缀
    'DB_PARAMS'         => array(), // 数据库连接参数    
    'DB_DEBUG'          => TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'   => false, // 启用字段缓存
    'DB_CHARSET'        => 'utf8', // 数据库编码默认采用utf8
//    'SHOW_PAGE_TRACE'   => true,
    'PAGE_SIZE'         => 20,
    'PAGE_THEME'        => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    'TMPL_CACHE_ON'     => false, // 是否开启模板编译缓存,设为false则每次都会重新编译
    'UPLOAD_SETTING'    => require __DIR__ . '/upload.php',
);
