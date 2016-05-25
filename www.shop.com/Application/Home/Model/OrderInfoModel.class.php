<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Model;

/**
 * Description of OrderInfoModel
 *
 * @author qingf
 */
class OrderInfoModel extends \Think\Model{
    public function addOrder() {
        $this->startTrans();
        $user_info = login();//获取用户信息
        //1.保存订单基本信息
        //1.1获取地址信息
        $address_info = D('Address')->getAddressInfo(I('post.address_id'));
        $this->data['name'] = $address_info['name'];
        $this->data['province_name'] = $address_info['province_name'];
        $this->data['city_name'] = $address_info['city_name'];
        $this->data['area_name'] = $address_info['area_name'];
        $this->data['detail_address'] = $address_info['detail_address'];
        $this->data['tel'] = $address_info['tel'];
        $this->data['member_id'] = $address_info['member_id'];
        
        //获取配送方式
        $pmconfig_model = new PmConfigModel();
        $delivery_info = $pmconfig_model->getDeliveryInfo($this->data['delivery_id']);
        $this->data['delivery_name']=$delivery_info['name'];
        $this->data['delivery_price']=$delivery_info['price'];
        
        //获取支付方式
        $payment_info = $pmconfig_model->getPaymentInfo($this->data['pay_type_id']);
        $this->data['pay_type_name'] = $payment_info['name'];
        
        //获取商品总价
        $cart_list = D('Cart')->getCartList();
        $this->data['price'] = $cart_list['total_price'];
        $this->data['status'] = 1;//订单状态-待支付
        if(($order_id = $this->add()) === false){
            $this->rollback();
            return false;
        }
        
        
        
        //2.保存订单详细信息
        $order_detail_list = [];
        foreach($cart_list['goods_list'] as $goods){
            $goods['goods_id'] = $goods['id'];
            unset($goods['id']);
            $goods['goods_name'] = $goods['name'];
            unset($goods['name']);
            $goods['price'] = $goods['shop_price'];
            unset($goods['shop_price']);
            $goods['total_price'] = $goods['sub_total'];
            unset($goods['sub_total']);
            $goods['order_info_id'] = $order_id;
            $order_detail_list[] = $goods;
        }
        if(M('OrderInfoItem')->addAll($order_detail_list)===false){
            $this->error = '保存订单详细信息失败';
            $this->rollback();
            return false;
        }
        
        
        
        //3.保存发票信息
        //获取发票类型:1是个人 2是企业
        $invoice_type = I('post.invoice_type');
        //发票抬头
        if($invoice_type == 2){
            $invoice_name = I('post.company_name');//公司发票抬头是公司名称
        } else {
            $invoice_name = $address_info['name'];//个人用户发票抬头就是收货人名字
        }
        
        //发票内容
        $invoice_content_type = I('post.invoice_content');
        $invoice_content = '';
        //根据不同的发票内容类型生成不同的详情
        switch ($invoice_content_type){
            case 1://明细
                foreach($cart_list['goods_list'] as $goods){
                    $invoice_content .= $goods['name']  . "\t"  . $goods['shop_price'] ."\t×\t" . $goods['amount'] . "\t" . $goods['sub_total'] . "\r\n";
                }
                break;
            case 2://办公用品
                $invoice_content .= "办公用品\r\n";
                break;
            case 3://体育用品
                $invoice_content .= "体育用品\r\n";
                break;
            case 4://耗材
                $invoice_content .= "耗材\r\n";
                break;
        }
        $invoice_content = $invoice_name."\r\n" . $invoice_content ."总计:". $cart_list['total_price'];
        //插入发票信息
        $data = [
            'name'=>$invoice_name,
            'content'=>$invoice_content,
            'price'=>$cart_list['total_price'],
            'inputtime'=>NOW_TIME,
            'order_info_id'=>$order_id,
            'member_id'=>$user_info['id'],
        ];
        if(M('Invoice')->add($data)===false){
            $this->error = '保存发票信息失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
}


/**
 * 
 * 四哥许坤
 * 
 * 三鹿牌肉粽   48.00  ×  10   480.00
 * 海天白葡萄酒 49.00  ×  2    98.00
 * 
 * 总计:578.00
 * 
 */