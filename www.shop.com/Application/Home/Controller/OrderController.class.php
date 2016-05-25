<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of OrderController
 *
 * @author qingf
 */
class OrderController extends \Think\Controller{
    /**
     * @var \Home\Model\OrderInfoModel 
     */
    private $_model = null;


    public function _initialize() {
        $this->_model = D('OrderInfo');
    }
    
    //添加订单
    public function add() {
        //收集数据
        if($this->_model->create() ===false){
            $this->error($this->_model->getError());
        }
        //保存数据
        if($this->_model->addOrder()===fasle){
            $this->error($this->_model->getError());
        }
        //跳转
        redirect(U('Cart/flow3'));
    }
}
