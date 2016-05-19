<?php

/**
 * 加盐加密
 * @param string $password 原始密码
 * @param string $salt     盐
 * @return string
 */
function salt_mcrypt($password,$salt){
    return md5(md5($password) . $salt);
}

/**
 * 保存/获取用户session信息.
 * @param array $data
 * @return null|array
 */
function login($data = null){
    if($data){
        session('USER_INFO',$data);
    }else{
        return session('USER_INFO');
    }
}