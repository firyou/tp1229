<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of GoodsController
 *
 * @author qingf
 */
class GoodsController extends \Think\Controller{
    //put your code here
    
    public function clickNum($id) {
        
        //使用redis存储点击数
        $key = 'goods_click';
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $goods_click = $redis->zincrBy($key,1,$id);
        $this->ajaxReturn($goods_click);
        exit;
        
        //使用mysql存储点击数
        //获取该商品的点击数
        //判断是否有记录
        $model = M('GoodsClick');
        if($click_times = $model->getFieldByGoodsId($id,'click_times')){
            ++$click_times;
            $cond = ['goods_id'=>$id];
            $model->where($cond)->setInc('click_times');
        }else{
            $click_times = 1;
            $data = [
                'goods_id'=>$id,
                'click_times'=>$click_times,
            ];
            
            $model->add($data);
        }
        $this->ajaxReturn($click_times);
    }
}
