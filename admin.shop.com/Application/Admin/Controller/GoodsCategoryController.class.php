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

    /**
     * 分类列表.
     */
    public function index() {
        //获取所有的可用的分类
        $this->assign('rows',$this->_model->getList());
        $this->display();
    }

    /**
     * 添加分类
     */
    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //添加数据
            if ($this->_model->addCategory() === false) {
                $this->error($this->_model->getError());
            }
            //提示跳转
            $this->success('添加成功', U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 修改分类.
     * @param integer $id
     */
    public function edit($id) {
        //保存
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //添加数据
            if ($this->_model->updateCategory() === false) {
                $this->error($this->_model->getError());
            }
            //提示跳转
            $this->success('修改成功', U('index'));
        } else {//回显
            //获取到当前记录
            $row = $this->_model->find($id);
            //获取所有的分类
            $this->_before_view();
            //分配数据.
            $this->assign('row', $row);
            //调用视图
            $this->display('add');
        }
    }

    /**
     * 物理删除商品分类.
     * @param type $id
     */
    public function delete($id) {
        if($this->_model->deleteCategory($id)===false){
            $this->error($this->_model->getError());
        }
        $this->success('删除成功', U('index'));
    }

    /**
     * 添加和编辑页面的通用操作抽取出来.
     */
    private function _before_view() {
        //获取所有的已有分类
        $goods_categories = $this->_model->getList();
        //添加一个顶级分类选项
        array_unshift($goods_categories, ['id'=>0,'name'=>'顶级分类','parent_id'=>null]);
        $this->assign('goods_categories', json_encode($goods_categories));
    }

}
