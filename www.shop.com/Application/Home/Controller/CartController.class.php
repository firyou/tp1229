<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of CartController
 *
 * @author qingf
 */
class CartController extends \Think\Controller{
    /**
     * @var \Home\Model\CartModel 
     */
    private $_model = null;


    public function _initialize() {
        $this->assign('action_name', ACTION_NAME);
        $this->_model = D('Cart');
    }
    /**
     * 将商品放入到购物车
     * [
     *  5=>2,
     * ]
     */
    public function addCart($id,$amount){
        //没有登录的时候,存入cookie中,只需要保存每个要买商品的id和数量
//        $key = 'CART';
//        $cart_list = cookie($key);
//        if(isset($cart_list[$id])){
//            $cart_list[$id] += $amount;
//        } else{
//            $cart_list[$id] = $amount;
//        }
//        cookie($key, $cart_list,604800);
        $this->_model->addCart($id, $amount);
        //完成后立即跳转,避免重复添加
        $this->success('添加成功',U('flow1'));
    }
    
    public function flow1(){
        $this->assign($this->_model->getCartList());
        $this->display();
    }
    
    /**
     * 填写收货信息.
     */
    public function flow2() {
        if(!login()){
            cookie('_self_',__SELF__);
            $this->error('请登录',U('Member/login'));
        }else{
            //读取收货地址
            $this->assign('address_list', D('Address')->getList());
            $this->display();
        }
    }
}
