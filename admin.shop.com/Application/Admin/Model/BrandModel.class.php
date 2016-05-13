<?php

namespace Admin\Model;

/**
 * Description of BrandModel
 *
 * @author qingf
 */
class BrandModel extends \Think\Model {
    protected $_validate = [
        ['name','require','品牌名称不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
        ['name','','品牌已存在',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH],
    ];

    /**
     * 获取分页结果集.
     * @param array $cond 查询条件.
     * @return array
     */
    public function getPageResult(array $cond = array()) {
        //获取总行数
        $cond = array_merge(['status'=>['gt',-1]],$cond);
        $count = $this->where($cond)->count();
        $size = C('PAGE_SIZE');
        //获取分页代码
        //>>创建分页对象
        $page = new \Think\Page($count,$size);
        //>>配置分页样式
        $page->setConfig('theme', C('PAGE_THEME'));
        //>>获取分页代码
        $page_html = $page->show();
        //获取当前页数据
        $rows = $this->where($cond)->page(I('get.p'),$size)->select();
        //返回分页代码和结果集
        return ['page_html'=>$page_html,'rows'=>$rows];
    }
    
    /**
     * 获取分类列表
     */
    public function getList(){
        return $this->where(['status'=>1])->select();
    }

}
