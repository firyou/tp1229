<?php

namespace Admin\Controller;

class GoodsCategoryController extends \Think\Controller {

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
        //获取所有的可用的分类
        $this->assign($this->_model->getPageResult());
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            //收集数据
            if($this->_model->create() === false){
                $this->error($this->_model->getError());
            }
            //添加数据
            if($this->_model->addCategory() === false){
                $this->error($this->_model->getError());
            }
            //提示跳转
            $this->success('添加成功',U('index'));
        } else {
            //获取所有的已有分类
            $this->assign('goods_categories', json_encode($this->_model->getList()));
            $this->display();
        }
    }

    public function edit($id) {
        
    }

    public function delete($id) {
        
    }

}
