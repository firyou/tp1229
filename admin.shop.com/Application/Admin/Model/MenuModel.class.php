<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of MenuModel
 *
 * @author qingf
 */
class MenuModel extends \Think\Model{
    public function getList(){
        return $this->where(['status'=>1])->select();
    }
    
    /**
     * 添加菜单.并保存对应的权限关联.
     * @return boolean
     */
    public function addMenu() {
        unset($this->data[$this->getPk()]);
        $this->startTrans();
        //保存菜单信息
        //使用nestedsets添加菜单
        $orm = D('NestedSetsMysql','Logic');
        //创建一个nestedsets对象
        $nestedsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        if(($menu_id = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom'))===false){
            $this->error = '添加菜单失败';
            $this->rollback();
            return false;
        }
        //保存关联的权限
        $permission_ids = I('post.permission_id');
        if($permission_ids){
            $data = [];
            foreach($permission_ids as $permission_id){
                $data[] = [
                    'menu_id'=>$menu_id,
                    'permission_id'=>$permission_id,
                ];
            }
            if(M('MenuPermission')->addAll($data) === false){
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
        
    }
    
    /**
     * 读取基本信息
     * 读取权限关联
     * @param type $id
     */
    public function getMenuInfo($id) {
        $row = $this->find($id);
        $row['permission_ids'] = json_encode(M('MenuPermission')->where(['menu_id'=>$id])->getField('permission_id',true));
        return $row;
    }
}
