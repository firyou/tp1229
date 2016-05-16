<?php
namespace Admin\Behaviors;

class checkPermissionBehavior extends \Think\Behavior{
    public function run(&$params) {
        //验证用户所拥有的权限是否包括当前页面.
        //1.
        $paths = array_merge([],C('URL_IGNORE'));
        //获取已登录用户的信息
        $admin_info = session('ADMIN_INFO');
        //如果已经登陆才需要查询数据表
        if($admin_info){
            //获取管理员id
            $admin_id = $admin_info['id'];
            //获取管理员所能够看到的请求url
            $sql = 'SELECT DISTINCT path FROM (SELECT  permission_id  FROM admin_role AS ar  LEFT JOIN role_permission AS rp  ON ar.`role_id` = rp.`role_id`  WHERE ar.`admin_id` = '.$admin_id.'  UNION SELECT permission_id  FROM admin_permission AS ap  WHERE ap.`admin_id` = '.$admin_id.') AS t  LEFT JOIN permission AS p  ON t.permission_id = p.`id` WHERE p.`path` <> "" ';
            $permissions_info = M()->query($sql);
            //将path地址形成一位数组
            if($permissions_info) {
                foreach ($permissions_info as $permission_info){
                    $paths[] = $permission_info['path'];
                }
            }
        }
        //判断当前的页面是否在paths中
        //获取当前请求的url地址
        $url = implode('/', [MODULE_NAME,CONTROLLER_NAME,ACTION_NAME]);
        /**
         * 1.如果没有权限 跳转到登陆页面(采用)
         * 2.提示错误,然后回退到上一个页面<script type="text/javascript">location.back()</script>
         */
        if(!in_array($url, $paths)){
            header('Content-Type:text/html;charset=utf-8');
            $url = U('Admin/Admin/login');
            redirect($url,1,'无权访问');
        }
    }
}
