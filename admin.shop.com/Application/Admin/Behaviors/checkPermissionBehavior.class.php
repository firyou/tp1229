<?php
namespace Admin\Behaviors;

class checkPermissionBehavior extends \Think\Behavior{
    public function run(&$params) {
        //验证用户所拥有的权限是否包括当前页面.
        //1.添加忽略列表
        $paths = array_merge([],C('URL_IGNORE'));
        //获取已登录用户的信息
        $admin_info = login();
        
        //session丢失了
        if(!$admin_info){
//            //自动登陆
//            $cookie_token = cookie('admin_token');
//            //验证cookie中的令牌和数据表中的是否一致
//            $admin_info = M('Admin')->where($cookie_token)->find();
//            //匹配,存session
//            if($admin_info){
//                login($admin_info);
//            }
            $admin_info = D('Admin')->autoLogin();//自动登陆
        }
        //如果已经登陆才需要查询数据表
        if($admin_info){
            if($admin_info['username'] == 'admin'){
                return true;
            }
            $paths = array_merge($paths,C('LOGIN_IGNORE'));
            //获取管理员id
            //获取到用户的可访问的path列表
//            $session_paths = permission_pathes();
            $paths = array_merge($paths,permission_pathes());
        }
        
        //判断当前的页面是否在paths中
        //获取当前请求的url地址
        $url = implode('/', [MODULE_NAME,CONTROLLER_NAME,ACTION_NAME]);
        /**
         * 1.如果没有权限 跳转到登陆页面(采用)
         * 2.提示错误,然后回退到上一个页面<script type="text/javascript">history.back()</script>
         */
        if(!in_array($url, $paths)){
            header('Content-Type:text/html;charset=utf-8');
//            $html = '<script type="text/javascript">alert("无权访问");history.back()</script>';
//            die($html);
            
            session(null);
            $url = U('Admin/Admin/login');
            redirect($url,1,'无权访问');
        }
    }
}
