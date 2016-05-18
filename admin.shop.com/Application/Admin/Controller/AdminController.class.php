<?php

namespace Admin\Controller;

class AdminController extends \Think\Controller {

    /**
     * @var \Admin\Model\AdminModel 
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('Admin');
        $meta_titles  = [
            'index' => '管理管理员',
            'add'   => '添加管理员',
            'edit'  => '修改管理员',
        ];
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理管理员';
        $this->assign('meta_title', $meta_title);
    }

    public function index() {
        $this->assign('rows', $this->_model->getList());
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->addAdmin() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('添加成功', U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 修改管理员.
     * 如需修改密码,才需要填写密码框
     * @param type $id
     */
    public function edit($id) {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->updateAdmin() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('修改成功', U('index'));
        } else {
            $this->assign('row', $this->_model->getAdminInfo($id));
            $this->_before_view();
            $this->display('add');
        }
    }

    /**
     * 删除管理员
     * 
     * @param type $id
     */
    public function delete($id) {
        if ($this->_model->deleteAdmin($id) === false) {
            $this->error($this->_model->getError());
        }
        $this->success('删除成功', U('index'));
    }

    private function _before_view() {
        //获取所有的角色
        $this->assign('roles', D('Role')->getList());
        //获取所有的所有权限
        $this->assign('permissions', json_encode(D('Permission')->getList()));
    }
    
    public function login() {
        if(IS_POST){
            //验证
            if($this->_model->create('','login')===false){
                $this->error($this->_model->getError());
            }
            if($this->_model->login() === false){
                $this->error($this->_model->getError());
            }
            $this->success('登陆成功',U('Index/index'));
        }else{
            $this->display();
        }
    }
    
    /**
     * 退出用户
     */
    public function logout(){
        $admin_info = login();
        $data = [
            'id'=>$admin_info['id'],
            'login_token'=>'',
        ];
        if($this->_model->setField($data)===false){
            $this->error($this->_model->getError());
        }else{
            session(null);
            cookie(null);
            $this->success('退出成功',U('login'));
        }
    }
    
    public function changePwd() {
        if(IS_POST){
            //收集数据
            if($this->_model->create()===false){
                $this->error($this->_model->getError());
            }
            if($this->_model->changePwd()===false){
                $this->error($this->_model->getError());
            }
            $this->success('修改成功',U('Index/main'));
        }else{
            //原始密码
            //新密码
            //确认新密码
            $this->display();
        }
    }

}
