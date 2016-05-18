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
class MenuModel extends \Think\Model {

    public function getList() {
        return $this->where(['status' => 1])->select();
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
        $orm        = D('NestedSetsMysql', 'Logic');
        //创建一个nestedsets对象
        $nestedsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        if (($menu_id    = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom')) === false) {
            $this->error = '添加菜单失败';
            $this->rollback();
            return false;
        }
        //保存关联的权限
        $permission_ids = I('post.permission_id');
        if ($permission_ids) {
            $data = [];
            foreach ($permission_ids as $permission_id) {
                $data[] = [
                    'menu_id'       => $menu_id,
                    'permission_id' => $permission_id,
                ];
            }
            if (M('MenuPermission')->addAll($data) === false) {
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    /**
     * 修改
     * @return boolean
     */
    public function saveMenu() {
        $request_data = $this->data;
        $this->startTrans();
        //1.判断是否修改了父级权限
        //保存菜单信息
        $parent_id    = $this->getFieldById($request_data['id'], 'parent_id');
        //1.1如果修改了,使用nestedsets计算新的左右节点和层级
        if ($parent_id != $request_data['parent_id']) {
            //创建orm
            $orm        = D('NestedSetsMysql', 'Logic');
            $nesetdsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
            if ($nesetdsets->moveUnder($request_data['id'], $request_data['parent_id'], 'bottom') === false) {
                $this->error = '父级分类不合法';
                $this->rollback();
                return false;
            }
        }
        //保存关联的权限
        $permission_ids        = I('post.permission_id');
        $menu_permission_model = M('MenuPermission');
        $menu_permission_model->where(['menu_id' => $request_data['id']])->delete();
        if ($permission_ids) {
            $data = [];
            foreach ($permission_ids as $permission_id) {
                $data[] = [
                    'menu_id'       => $request_data['id'],
                    'permission_id' => $permission_id,
                ];
            }
            if ($menu_permission_model->addAll($data) === false) {
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    /**
     * 删除菜单,及其子菜单.
     * 同时删除对应的权限关联.
     * @param integer $id
     * @return boolean
     */
    public function deleteMenu($id) {
        $this->startTrans();
        //获取所有的后代菜单
        //获取当前菜单的信息
        $info     = $this->field('lft,rght')->find($id);
        $cond     = [
            'lft'  => ['egt', $info['lft']],
            'rght' => ['elt', $info['rght']],
        ];
        $menu_ids = $this->where($cond)->getField('id', true);

        //创建orm
        $orm        = D('NestedSetsMysql', 'Logic');
        $nesetdsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nesetdsets->delete($id) === false) {
            $this->error = '删除菜单失败';
            $this->rollback();
            return false;
        }

        //删除关联的权限
        if (M('MenuPermission')->where(['menu_id' => ['in', $menu_ids]])->delete() === false) {
            $this->error = '删除权限失败';
            $this->rollback();
            return false;
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
        $row                   = $this->find($id);
        $row['permission_ids'] = json_encode(M('MenuPermission')->where(['menu_id' => $id])->getField('permission_id', true));
        return $row;
    }

    public function getMenus() {
        //获取当前用户的权限列表
        $pids = permission_ids();
        //获取所有的可访问菜单.
        return $this->field('id,name,path,parent_id')->distinct(true)->alias('m')->join('left join __MENU_PERMISSION__ as mp ON m.`id`=mp.`menu_id`')->where(['permission_id' => ['in', $pids]])->select();
    }

}
