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
            $this->display();
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
            $this->success('登录成功', U('Index/index'));
        } else {
            $this->display();
        }
    }

    public function sendSms($telphone) {
        //引入阿里大鱼的自动加载机制文件
        vendor('Alidayu.Autoloader');
        date_default_timezone_set('Asia/Shanghai');
        $c            = new \TopClient;
        $c->format='json';
        $c->appkey    = '23368733';
        $c->secretKey = '260d2fbb9cb7108421d096e6a6201853';
        $req          = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("四哥测试");
        $code = (string)mt_rand(1000, 9999);
        //将验证码保存到session中,以便验证
        session('REG_CODE',$code);
        $data = [
            'code'=>$code,
            'product'=>'啊咿呀哟',
        ];
        $req->setSmsParam(json_encode($data));
        $req->setRecNum($telphone);
        $req->setSmsTemplateCode("SMS_5590023");
        $resp         = $c->execute($req);
        if(isset($resp->result) && isset($resp->result->success)){
            $status = true;
        }else{
            $status = false;
        }
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

}
