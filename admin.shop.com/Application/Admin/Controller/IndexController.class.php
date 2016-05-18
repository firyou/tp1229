<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    
    public function top(){
        $this->display();
    }
    
    public function menu(){
        //取出所有能看到的菜单
        //菜单是根据权限来判断的的
        $menu_model = D('Menu');
        //1.获取权限id
        $menus = $menu_model->getMenus();
        $this->assign('menus', $menus);
        //2.获取菜单列表
        $this->display();
    }
    
    public function main(){
        $this->display();
    }
}