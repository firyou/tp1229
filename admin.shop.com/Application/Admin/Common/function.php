<?php

/**
 * 存储或者删除管理员信息
 * 如果传递了参数,表示设置,否则表示获取
 * @param type $data
 */
function login($data = null) {
    if ($data) {
        session('ADMIN_INFO', $data);
    } else {
        return session('ADMIN_INFO') ? session('ADMIN_INFO') : [];
    }
}

/**
 * 存储或者删除管理员授权操作路径
 * 如果传递了参数,表示设置,否则表示获取
 * @param type $data
 */
function permission_pathes($data = null) {
    if ($data) {
        session('PATHES', $data);
    } else {
        return session('PATHES') ? session('PATHES') : [];
    }
}

/**
 * 存储或者删除管理员授权权限id
 * 如果传递了参数,表示设置,否则表示获取
 * @param type $data
 */
function permission_ids($data = null) {
    if ($data) {
        session('PIDS', $data);
    } else {
        return session('PIDS') ? session('PIDS') : [];
    }
}
