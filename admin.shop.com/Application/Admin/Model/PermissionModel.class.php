<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of PermissionModel
 *
 * @author qingf
 */
class PermissionModel extends \Think\Model{
    protected $_validata = [
        ['name','require','权限名称不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
        ['parent_id','require','父级权限不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
    ];
    
    
    /**
     * 获取分类列表
     */
    public function getList(){
        return $this->where(['status'=>1])->order('lft')->select();
    }
    
    /**
     * 使用nestedsets添加权限.
     */
    public function addPermission() {
        unset($this->data[$this->getPk()]);
        //创建orm
        $orm = D('NestedSetsMysql','Logic');
        $nesetdsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        if($nesetdsets->insert($this->data['parent_id'], $this->data, 'bottom') === false){
            $this->error = '添加失败';
            return false;
        }
        return true;
    }
}
