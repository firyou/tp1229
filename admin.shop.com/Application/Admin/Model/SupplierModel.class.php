<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of SupplierModel
 *
 * @author qingf
 */
class SupplierModel extends \Think\Model {

    protected $_validate = [
        ['name', 'require', '供应商名称不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['name', '', '供应商已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH],
    ];

    /**
     * 获取分页列表.
     * @param array $cond  查询条件.
     * @return array 当前页面内容和分页代码.
     */
    public function getPageResult(array $cond = []) {
        $cond = array_merge(['status'=>1],$cond);
        $page_size = C('PAGE_SIZE');// 获取页尺寸配置.
        $count     = $this->where($cond)->count();// 获取总行数.
        $page      = new \Think\Page($count, $page_size);// 创建分页工具类对象.
        $page->setConfig('theme', C('PAGE_THEME'));// 设定分页风格.
        $page_html = $page->show(); //获得分页html代码.
        $rows      = $this->where($cond)->page(I('get.p'), $page_size)->select();//获取当前页结果集.
        return ['rows' => $rows, 'page_html' => $page_html];
    }
    
    public function addSupplier(){
        unset($this->data[$this->pk]);
        return $this->add();
    }

}
