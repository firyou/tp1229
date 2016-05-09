<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * Description of BrandController
 *
 * @author qingf
 */
class BrandController extends \Think\Controller {

    /**
     * @var \Admin\Model\BrandModel 
     */
    private $_model = null;

    /**
     * 初始化控制器的时候自动执行的代码.
     */
    protected function _initialize() {
        $this->_model = D('Brand');
        $meta_titles  = [
            'index' => '管理品牌',
            'add'   => '添加品牌',
            'edit'  => '修改品牌',
        ];
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理品牌';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 列表页
     */
    public function index() {
        //获取数据
        //准备查询条件
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = ['like', '%' . $name . '%'];
        }
        $this->assign($this->_model->getPageResult($cond));
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //添加到数据表
            if ($this->_model->add() === false) {
                $this->error($this->_model->getError());
            }
            //提示跳转
            $this->success('添加成功', U('index'));
        } else {
            $this->display();
        }
    }

    /**
     * 修改品牌.
     * @param integer $id 品牌id.
     */
    public function edit($id) {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //添加到数据表
            if ($this->_model->save() === false) {
                $this->error($this->_model->getError());
            }
            //提示跳转
            $this->success('修改成功', U('index'));
        } else {
            //取出数据
            $row = $this->_model->find($id);
            $this->assign('row', $row);
            $this->display('add');
        }
    }

    public function delete($id) {
        $data = [
            'id'=>$id,
            'status'=>-1,
            'name'=>['exp','CONCAT(`name`,"_del")'],
        ];
        if($this->_model->setField($data)===false){
            $this->error($this->_model->getError());
        }else{
            //提示跳转
            $this->success('删除成功', U('index'));
        }
    }

}
