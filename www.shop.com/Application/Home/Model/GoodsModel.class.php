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
        return $row;
    }

}
