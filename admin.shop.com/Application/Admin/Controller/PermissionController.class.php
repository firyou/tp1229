<?php

namespace Admin\Controller;

class PermissionController extends \Think\Controller {

    /**
     * @var \Admin\Model\PermissionModel 
     */
    private $_model = null;

    /**
     * 初始化控制器的时候自动执行的代码.
     */
    protected function _initialize() {
        $this->_model = D('Permission');
        $meta_titles  = [
            'index' => '管理权限',
            'add'   => '添加权限',
            'edit'  => '修改权限',
        ];
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理权限';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 权限列表页面.
     * 使用了treegrid展示.
     */
    public function index() {
        $this->assign('rows', $this->_model->getList());
        $this->display();
    }

    /**
     * 添加权限.
     */
    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->addPermission() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('添加成功', U('index'));
        } else {
            //读取权限列表
            $permissions = $this->_model->getList();
            array_unshift($permissions, ['id' => 0, 'name' => '顶级权限', 'parent_id' => null]);
            $this->assign('permissions', json_encode($permissions));
            $this->display();
        }
    }

    public function edit($id) {
        $this->display('add');
    }

    public function delete($id) {
        
    }

}
