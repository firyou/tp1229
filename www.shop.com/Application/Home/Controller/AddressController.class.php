<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of AddressController
 *
 * @author qingf
 */
class AddressController extends \Think\Controller{
    protected function _initialize() {
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
    public function index() {
        $this->display();
    }
    
    /**
     * 添加收货人地址.
     */
    public function add() {
        if(IS_POST){
            
        }else{
            //读取所有省份
            $provinces = D('Locations')->getListByParentId(0);
            $this->assign('provinces', $provinces);
            $this->display();
        }
    }
    
    
    public function getListByParentId($id) {
        $model = D('Locations');
        $list = $model->getListByParentId($id);
        $this->ajaxReturn($list);
    }
}
