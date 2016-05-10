<?php
namespace Admin\Model;

class GoodsCategoryModel extends \Think\Model{
    /**
     * TODO:自动验证
     * name不能为空
     */
    
    /**
     * 获取分类列表
     */
    public function getList(){
        return $this->where(['status'=>1])->order('lft')->select();
    }
}
