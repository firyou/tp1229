<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * 菜单和权限的关联模块
 * 某个菜单[添加商品] 只有权限为5的才能够看到
 * 我们要将 添加商品菜单4=>权限5保存到数据表中
 */
class MenuController extends \Think\Controller{
    /**
     * @var \Admin\Model\MenuModel 
     */
    private $_model = null;

    /**
     * 初始化控制器的时候自动执行的代码.
     */
    protected function _initialize() {
        $this->_model = D('Menu');
        $meta_titles  = [
            'index' => '管理菜单',
            'add'   => '添加菜单',
            'edit'  => '修改菜单',
        ];
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理菜单';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 菜单列表.
     */
    public function index() {
        //获取所有的可用的菜单
        $this->assign('rows',$this->_model->getList());
        $this->display();
    }

    /**
     * 添加菜单
     */
    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //添加数据
            if ($this->_model->addMenu() === false) {
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
     * 修改菜单.
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
            //获取到当前记录,以及权限关联
            $row = $this->_model->getMenuInfo($id);
            //获取所有的菜单
            $this->_before_view();
            //分配数据.
            $this->assign('row', $row);
            //调用视图
            $this->display('add');
        }
    }

    /**
     * 物理删除菜单.
     * @param type $id
     */
    public function delete($id) {
        $this->_model->deleteCategory($id);
    }

    /**
     * 添加和编辑页面的通用操作抽取出来.
     */
    private function _before_view() {
        //获取所有的已有菜单
        $goods_categories = $this->_model->getList();
        //添加一个顶级菜单选项
        array_unshift($goods_categories, ['id'=>0,'name'=>'顶级菜单','parent_id'=>null]);
        $this->assign('goods_categories', json_encode($goods_categories));
        
        //读出所有的权限
        $permissions = D('Permission')->getList();
        $this->assign('permissions', json_encode($permissions));
    }
}
