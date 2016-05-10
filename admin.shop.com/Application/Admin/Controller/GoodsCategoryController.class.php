<?php

namespace Admin\Controller;

class GoodsCategoryController extends \Think\Controller{
    /**
     * @var \Admin\Model\GoodsCategoryModel 
     */
    private $_model = null;

    /**
     * 初始化控制器的时候自动执行的代码.
     */
    protected function _initialize() {
        $this->_model = D('GoodsCategory');
        $meta_titles  = [
            'index' => '管理商品分类',
            'add'   => '添加商品分类',
            'edit'  => '修改商品分类',
        ];
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理商品分类';
        $this->assign('meta_title', $meta_title);
    }
    public function index() {
        
    }
    
    
    public function add() {
        //获取所有的已有分类
        $this->assign('goods_categories',json_encode($this->_model->getList()));
        $this->display();
    }
    
    public function edit($id) {
        
    }
    
    public function delete($id) {
        
    }
}
