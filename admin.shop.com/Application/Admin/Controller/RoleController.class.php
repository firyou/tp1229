<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * Description of RoleController
 *
 * @author qingf
 */
class RoleController extends \Think\Controller{
    /**
     * @var \Admin\Model\RoleModel 
     */
    private $_model = null;

    /**
     * 初始化控制器的时候自动执行的代码.
     */
    protected function _initialize() {
        $this->_model = D('Role');
        $meta_titles  = [
            'index' => '管理角色',
            'add'   => '添加角色',
            'edit'  => '修改角色',
        ];
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理角色';
        $this->assign('meta_title', $meta_title);
    }
    
    /**
     * 角色列表.
     */
    public function index() {
        //获取所有的可用的角色
        $this->assign('rows',$this->_model->getList());
        $this->display();
    }

    /**
     * 添加角色
     */
    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //添加数据
            if ($this->_model->addRole() === false) {
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
     * 修改角色.
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
            if ($this->_model->updateRole() === false) {
                $this->error($this->_model->getError());
            }
            //提示跳转
            $this->success('修改成功', U('index'));
        } else {//回显
            //获取到当前记录
            $row = $this->_model->getRoleInfo($id);
            //获取所有的角色
            $this->_before_view();
            //分配数据.
            $this->assign('row', $row);
            //调用视图
            $this->display('add');
        }
    }

    /**
     * 物理删除角色.
     * @param type $id
     */
    public function delete($id) {
        if($this->_model->deleteRole($id)===false){
            $this->error($this->_model->getError());
        }else{
            $this->success('删除成功', U('index'));
        }
    }

    /**
     * 添加和编辑页面的通用操作抽取出来.
     */
    private function _before_view() {
        //获取所有的所有权限
        $this->assign('permissions', json_encode(D('Permission')->getList()));
    }
}
