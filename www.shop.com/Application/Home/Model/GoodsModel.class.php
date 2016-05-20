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

}
