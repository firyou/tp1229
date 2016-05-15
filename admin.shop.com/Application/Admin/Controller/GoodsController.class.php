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

    /**
     * 列表页面
     * TODO:搜索
     */
    public function index() {
        //组织查询条件
        $cond              = [];
        $goods_category_id = I('get.goods_category_id');
        if ($goods_category_id) {
            $cond['goods_category_id'] = $goods_category_id;
        }
        $brand_id = I('get.brand_id');
        if ($brand_id) {
            $cond['brand_id'] = $brand_id;
        }
        $is_on_sale = I('get.is_on_sale');
        if (strlen($is_on_sale)) {
            $cond['is_on_sale'] = $is_on_sale;
        }
        $keyword = I('get.keyword');
        if ($keyword) {
            $cond['name'] = ['like', '%' . $keyword . '%'];//coreseek  sphinx(斯芬克斯)
        }
        //商品推荐状态的搜索
        //促销 goods_status&4
        $goods_status = I('get.goods_status');
        if($goods_status){
            $cond[] = 'goods_status & ' .$goods_status;
        }

        $this->assign($this->_model->getPageResult($cond));
        //获取所有分类
        //加载商品分类列表
        $this->assign('goods_categories', D('GoodsCategory')->getList());
        //加载商品品牌列表
        $this->assign('brands', D('Brand')->getList());
        $this->assign('goods_statuses', $this->_model->goods_statuses);
        $this->assign('is_on_sales', $this->_model->is_on_sales);
        $this->display();
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
            $this->success('添加成功', U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 编辑商品.
     * @param integer $id 商品id.
     */
    public function edit($id) {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->updateGoods() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('修改成功', U('index'));
        } else {
            $row = $this->_model->getGoodsInfo($id);
            $this->assign('row', $row);
            $this->_before_view();
            $this->display('add');
        }
    }

    /**
     * 删除商品,使用逻辑删除
     * @param integer $id
     */
    public function delete($id) {
        if ($this->_model->setField(['id' => $id, 'status' => 0]) === false) {
            $this->error($this->_model->getError());
        } else {
            $this->success('删除成功', U('index'));
        }
    }

    private function _before_view() {
        //加载商品分类列表
        $this->assign('goods_categories', json_encode(D('GoodsCategory')->getList()));
        //加载商品品牌列表
        $this->assign('brands', D('Brand')->getList());
        //加载商品供货商列表
        $this->assign('suppliers', D('Supplier')->getList());
    }

}
