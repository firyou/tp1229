<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of MemberController
 *
 * @author qingf
 */
class MemberController extends \Think\Controller {

    /**
     * @var \Home\Model\MemberModel 
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('Member');

        $meta_titles = [
            'register' => '用户注册',
            'login'    => '用户登录',
            'logout'   => '退出',
        ];
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '用户注册';
        $this->assign('meta_title', $meta_title);
    }

    public function register() {
        if (IS_POST) {
            //1.收集数据
            if ($this->_model->create('', 'reg') === false) {
                $this->error($this->_model->getError());
            }
            //2.添加数据
            if ($this->_model->addMember() === false) {
                $this->error($this->_model->getError());
            }
            //3.提示跳转
            $this->success('注册成功,请登录', U('login'));
        } else {
            $this->display('register_1');
        }
    }

    public function login() {
        if (IS_POST) {
            //1.收集数据
            if ($this->_model->create('', 'login') === false) {
                $this->error($this->_model->getError());
            }
            //2.添加数据
            if ($this->_model->login() === false) {
                $this->error($this->_model->getError());
            }
            //3.提示跳转
            $url = cookie('_self_')?cookie('_self_'):U('Index/index');//看是否从购物车过来的,如果是跳转回购物车
            cookie('_self_',null);//购物车地址使用过就可以销毁了
            $this->success('登录成功', $url);
        } else {
            $this->display();
        }
    }

    public function sendSms($telphone) {
        $code = (string)mt_rand(1000, 9999);
        //将验证码保存到session中,以便验证
        session('REG_CODE',$code);
        $data = [
            'code'=>$code,
            'product'=>'啊咿呀哟',
        ];
        $status = sendSms($telphone, $data);
        $this->ajaxReturn($status);
    }
    
    public function checkExist() {
        $cond = I('get.');
        if($this->_model->where($cond)->count()){
            $this->ajaxReturn(false);
        }else{
            $this->ajaxReturn(true);
        }
    }
    
    /**
     * 通过ajax返回数据
     */
    public function getUserInfo() {
        $userinfo = login();
        if($userinfo){
            $username= $userinfo['username'];
        }else{
            $username = '';
        }
        $this->ajaxReturn($username);
    }
    /**
     * 通过ajax返回数据
     */
    public function logout() {
        session(null);
        cookie(null);
        $this->success('退出成功',U('Index/index'));
    }

}
