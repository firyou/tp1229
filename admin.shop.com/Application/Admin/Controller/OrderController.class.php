<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * Description of OrderController
 *
 * @author qingf
 */
class OrderController extends \Think\Controller{
    /**
     * 获取订单列表
     */
    public function index(){
        $order_info = M('OrderInfo')->order('id DESC')->select();
        $this->assign('rows', $order_info);
        $this->display();
    }
    
    /**
     * 发货.
     * 模拟发货,并不会填写发货表单,以及存储快单信息.
     * @param type $id
     */
    public function send($id){
        if(M('OrderInfo')->where(['id'=>$id,'status'=>2])->setField('status',3)===false){
            $this->error('发货失败');
        }else{
            $this->success('发货成功',U('index'));
        }
    }
}
