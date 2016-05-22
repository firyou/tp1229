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
        $redis = getRedis();
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
    
    /**
     * 将redis的点击数同步到数据库中
     * 1.从redis中获取所有的点击数
     * 2.遍历获取所有的键名
     * 3.删除数据库中同名的商品记录
     * 4.将redis中的存进去
     * @param type $param
     */
    public function syncClicks() {
        $redis = getRedis();
        $key = 'goods_click';
        $click_list = $redis->zRange($key,0,-1,true);
        $goods_ids = array_keys($click_list);
        $model = M('GoodsClick');
        
        $model->where(['goods_id'=>['in',$goods_ids]])->delete();
        $data = [];
        foreach($click_list as $goods_id=>$click_times){
            $data[] = [
                'goods_id'=>$goods_id,
                'click_times'=>$click_times,
            ];
        }
        $model->addAll($data);
        echo '<script type="text/javascript">window.close();</script>';
    }
    
    /**
     * crontab -e
     * 分钟 小时 天 月 星期 命令
     * * /5 2-8  3,5,7 curl http://www.shop.com/goods/syncClicks 
     * wget 
     * 浏览器 地址
     */
}
