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
class OrderController extends \Think\Controller {

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
        if ($this->_model->create() === false) {
            $this->error($this->_model->getError());
        }
        //保存数据
        if ($this->_model->addOrder() === false) {
            $this->error($this->_model->getError());
        }
        //跳转
        redirect(U('Cart/flow3'));
    }

    /**
     * 订单列表.
     */
    public function index() {
        //获取用户的订单列表
        $order_list = $this->_model->getOrderList();
        $this->assign('rows', $order_list);
        //获取订单状态
        $this->assign('statuses', $this->_model->statuses);
        //获取分类列表
        $this->_beforeShow();
        $this->display();
    }

    /**
     * 获取分类列表
     * 获取帮助文章列表
     */
    private function _beforeShow() {
        //首页才展示分类列表.
        $this->assign('is_show_cat', false);

        //由于文章分类和帮助文章并不会经常更新,所以我们可以使用数据缓存 标识是ART_CATS
        $goods_categories = S('GOODS_CAT');
        if (!$goods_categories) {
            //取出分类
            $goods_categories = M('GoodsCategory')->where(['status' => 1])->select();
            S('GOODS_CAT', $goods_categories, 300);
        }


        $article_categories = S('ART_CATS');
        if (!$article_categories) {
            //获取帮助文章分类
            $article_categories = M('ArticleCategory')->where(['is_help' => 1, 'status' => 1])->getField('id,name');
            S('ART_CATS', $article_categories, 300);
        }
        //获取各分类的文章
        $artilce_list = S('ART_LIST');
        if (!$artilce_list) {
            foreach ($article_categories as $article_cat_id => $article_cat) {
                $artilce_list[$article_cat_id] = M('Article')->where(['article_category_id' => $article_cat_id])->limit(6)->getField('id,id,name');
            }
            S('ART_LIST', $artilce_list, 300);
        }

        //传入视图
        $this->assign('goods_categories', $goods_categories);
        $this->assign('article_categories', $article_categories);
        $this->assign('article_list', $artilce_list);
    }

    /**
     * 确认收货
     * @param type $id
     */
    public function shouhuo($id) {
        if ($this->_model->where(['id' => $id, 'status' => 3])->setField('status', 4) === false) {
            $this->error('收货异常');
        } else {
            $this->success('收货成功', U('index'));
        }
    }

    /**
     * 清空超时未支付订单
     * 
     * inputtime <   now_time-900
     */
    public function autoClearTimeoutOrder_tmp() {
        $cond               = [
            'status'    => 1,
            'inputtime' => ['lt', NOW_TIME - 900],
        ];
        //1状态改为已关闭
        //1.1获取超时订单列表,为还原库存做准备
        $order_ids          = $this->_model->where($cond)->getField('id', true);
        //1.2状态改为0
        $this->_model->where($cond)->setField('status', 0);
        dump($order_ids);
        //2将库存还原
        //2.1取出指定订单的商品列表
        $goods_model        = M('Goods');
        $order_detail_model = M('OrderInfoItem');
        foreach ($order_ids as $order_id) {
            //2.1.1获取当前订单的商品列表
            $goods_list = $order_detail_model->where(['order_info_id' => $order_id])->field('goods_id,amount')->select();
            foreach ($goods_list as $goods) {
                $goods_model->where(['id' => $goods['goods_id']])->setInc('stock', $goods['amount']);
            }
        }
    }

    /**
     * 清空超时未支付订单
     * 15分钟超时
     * inputtime   <   now_time-900
     */
    public function autoClearTimeoutOrder() {
        $cond      = [
            'status'    => 1,
            'inputtime' => ['lt', NOW_TIME - 900],
        ];
        //1状态改为已关闭
        //1.1获取超时订单列表,为还原库存做准备
        $order_ids = $this->_model->where($cond)->getField('id', true);
        if (empty($order_ids)) {
            return true;
        }

        //1.2状态改为0
        $this->_model->where($cond)->setField('status', 0);
        dump($order_ids);
        //2将库存还原
        //2.1取出指定订单的商品列表
        $goods_model        = M('Goods');
        $order_detail_model = M('OrderInfoItem');
        //2.1.1获取当前订单的商品列表
        $goods_list         = $order_detail_model->where(['order_info_id' => ['in', $order_ids]])->field('goods_id,amount')->select();
        /**
         * [
         *    gid=>amount
         * ]
         */
        $list               = [];
        foreach ($goods_list as $goods) {
            //判断是否已经有这个商品的元素了
            if (isset($list[$goods['goods_id']])) {
                $list[$goods['goods_id']] += $goods['amount'];
            } else {
                $list[$goods['goods_id']] = $goods['amount'];
            }
        }
        
        //将库存还原.
        foreach($list as $gid=>$amount){
            $goods_model->where(['id'=>$gid])->setInc('stock',$amount);
        }
    }

}
