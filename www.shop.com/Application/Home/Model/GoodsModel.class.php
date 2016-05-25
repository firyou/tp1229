<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Model;

/**
 * Description of GoodsModel
 *
 * @author qingf
 */
class GoodsModel extends \Think\Model {

    public function getGoodsListByStatus($status) {
        $cond[] = 'goods_status&' . $status;
        $cond['is_on_sale'] = 1;
        $cond['status'] = 1;
        return $this->where($cond)->select();
    }
    
    /**
     * 获取商品详细信息
     * @param type $id
     * @return type
     */
    public function getGoodsInfo($id){
        //获取商品基本信息
        //获取商品详细描述
        //获取商品相册
        $row           = $this->field('g.*,gi.content,b.name as bname')->alias('g')->join('__GOODS_INTRO__ AS gi ON gi.goods_id=g.id')->join('__BRAND__ as b on g.brand_id=b.id')->where(['g.id'=>$id])->find();
        $gallery_model = M('GoodsGallery');
        $row['paths']  = $gallery_model->where(['goods_id' => $id])->getField('path', true);
        
        //会员价格
        $member_price_list = M('MemberGoodsPrice')->where(['goods_id'=>$id])->getField('member_level_id,price');
        //获取会员折扣率
        $discount_list = M('MemberLevel')->where(['status'=>1])->getField('id,name,discount');
        $list = [];//会员价格列表
        foreach($discount_list as $level_id=>$level_info){
            //如果为此商品的会员单独设置了价格,就优先使用
            if(isset($member_price_list[$level_id])){
                $list[] = [
                    'name'=>$level_info['name'],
                    'price'=>$member_price_list[$level_id],
                ];
            }else{//如果没有单独配置,就是用通用的折扣
                $list[] = [
                    'name'=>$level_info['name'],
                    'price'=>$row['shop_price'] * $level_info['discount'] / 100,
                ];
            }
        }
        $row['member_price_list'] = $list;
        
        return $row;
    }
    
    /**
     * 获取会员价格
     * @param type $id
     */
    public function getMemberPrice($id){
        //获取原始价格
        //获取会员折扣率
        //获取商品会员价
    }

}
