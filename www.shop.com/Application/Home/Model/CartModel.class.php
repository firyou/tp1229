<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Model;

/**
 * Description of CartModel
 *
 * @author qingf
 */
class CartModel extends \Think\Model {

    protected $tableName = 'shopping_car';

    public function getCartList_tmp() {
        //登录状态
        //未登录状态
        $cart_list   = cookie('CART');
        $data        = [];
        //取出商品的信息
        $goods_list  = []; //保存的商品信息
        $total_price = 0; //总计
        $goods_model = M('Goods');
        //每个商品发一次查询详细信息的sql,比较浪费资源
        foreach ($cart_list as $gid => $amount) {
            $goods_info              = $goods_model->find($gid);
            $goods_info['amount']    = $amount;
            $goods_info['sub_total'] = $goods_info['shop_price'] * $amount;
            $total_price+=$goods_info['sub_total'];
            $goods_list[]            = $goods_info;
        }
        $data = [
            'total_price' => $total_price,
            'goods_list'  => $goods_list
        ];
        dump($data);
    }

    public function getCartList() {
        $userinfo = login();
        if ($userinfo) {
            $cart_list = $this->where(['member_id' => $userinfo['id']])->getField('goods_id,amount');
        } else {
            //未登录
            $cart_list = cookie('CART');
        }
        $total_price = 0;
        $goods_list  = [];
        if ($cart_list) {
            //商品id列表
            $goods_ids       = array_keys($cart_list);
            //查询出商品信息
            $goods_info_list = M('Goods')->where(['id' => ['in', $goods_ids]])->select();
            //组织数据,保存小计金额\总计金额\每个商品的购买数量
            foreach ($goods_info_list as $goods) {
                $goods['shop_price'] = money_format($goods['shop_price']);
                $goods['amount']     = $cart_list[$goods['id']]; //数量
                $goods['sub_total']  = money_format($goods['shop_price'] * $cart_list[$goods['id']]); //小计
                $total_price += $goods['sub_total']; //总计
                $goods_list[]        = $goods; //保存到所有的商品信息数组中
            }
        }
        //为了返回多个数据,我们使用数组组织
        $data = [
            'total_price' => money_format($total_price),
            'goods_list'  => $goods_list,
        ];
        return $data;
    }

    /**
     * 添加到购物车
     * 如果已经在购物车就加数量
     * 如果没有在购物车就加记录
     * @param integer $id 商品id
     * @param integer $amount 购买数量
     */
    public function addCart($id, $amount) {
        $userinfo = login();
        //登陆用户保存购物车数据到数据库
        if ($userinfo) {
            $cond = [
                'memeber_id' => $userinfo['id'],
                'goods_id'   => $id,
            ];
//            $amount = $this->where($cond)->getField('amount');
            if ($this->where($cond)->getField('amount')) {
                $this->where($cond)->setInc('amount', $amount);
            } else {
                $data = [
                    'member_id' => $userinfo['id'],
                    'goods_id'  => $id,
                    'amount'    => $amount,
                ];
                $this->add($data);
            }
        } else {//未登录用户保存购物车数据到cookie
            $key       = 'CART';
            $cart_list = cookie($key);
            if (isset($cart_list[$id])) {
                $cart_list[$id] += $amount;
            } else {
                $cart_list[$id] = $amount;
            }
            cookie($key, $cart_list, 604800);
        }
    }

    /**
     * 用户登录的时候将cookie数据同步到数据库
     */
    public function cookie2Db() {
        //从cookie中获取购物车数据
        $key       = 'CART';
        $cart_list = cookie($key);
        //如果cookie中没有商品就无需执行后续的购物车处理
        if(!$cart_list){
            return true;
        }
        $goods_ids = array_keys($cart_list);
        //获取用户信息
        $userinfo = login();
        $cond = [
            'member_id'=>$userinfo['id'],
            'goods_id'=>['in',$goods_ids],
        ];
        $this->where($cond)->delete();
        //将购物车数据组织成一个二维数组
        $data = [];
        foreach ($cart_list as $gid=>$amount){
            $data[]= [
                'goods_id'=>$gid,
                'amount'=>$amount,
                'member_id'=>$userinfo['id'],
            ];
        }
        $this->addAll($data);
        //把cookie中的数据销毁
        cookie($key,null);
    }

}
