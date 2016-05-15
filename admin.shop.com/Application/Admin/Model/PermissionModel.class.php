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
class PermissionModel extends \Think\Model {

    protected $_validata = [
        ['name', 'require', '权限名称不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['parent_id', 'require', '父级权限不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
    ];

    /**
     * 获取分类列表
     */
    public function getList() {
        return $this->where(['status' => 1])->order('lft')->select();
    }

    /**
     * 使用nestedsets添加权限.
     */
    public function addPermission() {
        unset($this->data[$this->getPk()]);
        //创建orm
        $orm        = D('NestedSetsMysql', 'Logic');
        $nesetdsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nesetdsets->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->error = '添加失败';
            return false;
        }
        return true;
    }

    /**
     * 编辑权限.
     * @return boolean
     */
    public function savePermission() {
        $request_data = $this->data;
        //1.判断是否修改了父级权限
        $parent_id    = $this->getFieldById($request_data['id'], 'parent_id');
        //1.1如果修改了,使用nestedsets计算新的左右节点和层级
        if ($parent_id != $request_data['parent_id']) {
            //创建orm
            $orm        = D('NestedSetsMysql', 'Logic');
            $nesetdsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
            if ($nesetdsets->moveUnder($request_data['id'], $request_data['parent_id'], 'bottom') === false) {
                $this->error = '父级分类不合法';
                return false;
            }
        }
        //1.2如果没有修改,直接保存
        return $this->save();
    }

    /**
     * 删除权限.使用物理删除
     * @param type $id
     * @return boolean
     */
    public function deletePermission($id) {
        $orm        = D('NestedSetsMysql', 'Logic');
        $nesetdsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nesetdsets->delete($id) === false) {
            $this->error = '删除失败';
            return false;
        } else {
            return true;
        }
    }

}
