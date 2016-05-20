<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {

    protected function _initialize(){
        //取出分类
        $goods_categories = M('GoodsCategory')->where(['status' => 1])->select();
        //传入视图
        $this->assign('goods_categories', $goods_categories);
        //首页才展示分类列表.
        if(ACTION_NAME=='index'){
            $is_show_cat = true;
        }else{
            $is_show_cat = false;
        }
        $this->assign('is_show_cat', $is_show_cat);
    }
    public function index() {
        
        $this->display();
    }
    
    public function goods($id){
        $this->display();
    }

}
