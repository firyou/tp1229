<?php

return array(
    //'配置项'=>'配置值'
    //不使用RBAC限制的地址
    'URL_IGNORE' => [
        'Admin/Admin/login',
        'Admin/Captcha/captcha',
    ],
    //登陆用户都可见页面地址
    'LOGIN_IGNORE'=>[
        'Admin/Index/index',
        'Admin/Index/top',
        'Admin/Index/menu',
        'Admin/Index/main',
        'Admin/Admin/logout',
    ],
);
