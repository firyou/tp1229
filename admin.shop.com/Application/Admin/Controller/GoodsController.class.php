<?php

namespace Admin\Controller;

class GoodsController extends \Think\Controller {

    /**
     * @var \Admin\Model\GoodsModel 
     */
    private $_model = null;

    /**
     * 初始化控制器的时候自动执行的代码.
     */
    protected function _initialize() {
        $this->_model = D('Goods');
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

    /**
     * 添加商品.
     */
    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->addGoods() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('添加成功',U('index'));
        } else {
            //加载商品分类列表
            $this->assign('goods_categories', json_encode(D('GoodsCategory')->getList()));
            //加载商品品牌列表
            $this->assign('brands', D('Brand')->getList());
            //加载商品供货商列表
            $this->assign('suppliers', D('Supplier')->getList());
            $this->display();
        }
    }

    public function edit($id) {
        
    }

    public function delete($id) {
        
    }

}
