<?php

namespace Admin\Controller;

class GoodsController extends \Think\Controller {

    /**
     * @var \Admin\Model\ArticleCategoryModel 
     */
    private $_model = null;

    /**
     * 初始化控制器的时候自动执行的代码.
     */
    protected function _initialize() {
        $this->_model = D('ArticleCategory');
        $meta_titles  = [
            'index' => '管理商品',
            'add'   => '添加商品',
            'edit'  => '修改商品',
        ];
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理商品';
        $this->assign('meta_title', $meta_title);
    }
    
    public function index() {
        
    }
    
    public function add() {
        $this->display();
    }
    
    public function edit($id) {
        
    }
    
    public function delete($id) {
        
    }

}
