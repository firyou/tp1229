<?php
namespace Admin\Model;

class RoleModel extends \Think\Model{
    /**
     * TODO:自动验证
     * name不能为空
     */
    
    /**
     * 获取角色列表
     */
    public function getList(){
        return $this->where(['status'=>1])->select();
    }
    
    public function addRole(){
        unset($this->data[$this->getPk()]);
        $this->startTrans();
        //保存基本信息
        if(($role_id = $this->add())===false){
            $this->rollback();
            return false;
        }
        //保存关联关系
        if($this->_save_permission($role_id)===false){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    
    /**
     * 修改角色.
     * @return boolean
     */
    public function updateRole() {
        $this->startTrans();
        $request_data = $this->data;
        //保存基本信息
        if($this->save()===false){
            $this->rollback();
            return false;
        }
        //保存关联关系
        if($this->_save_permission($request_data['id'],false) === false){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    
    /**
     * 保存角色和权限的关联关系.
     * @param integer $role_id 角色id.
     * @param boolean $is_new 是新增角色还是编辑角色.
     * @return boolean
     */
    private function _save_permission($role_id,$is_new=true){
        $role_permission_model = M('RolePermission');
        //如果是修改,就先删除原来的
        if(!$is_new){
            if($role_permission_model->where(['role_id'=>$role_id])->delete()===false){
                $this->error = '删除原权限失败';
                return false;
            }
        }
        //收集用户提交的权限列表
        $permission_ids = I('post.permission_id');
        if(empty($permission_ids)){
            return true;
        }
        $data = [];
        //准备可以一次插入多条记录的数据结构
        foreach($permission_ids as $permission_id){
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        if($role_permission_model->addAll($data)===false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    /**
     * 物理方式删除角色.
     * @param integer $id 当前角色id.
     * @return type
     */
    public function deleteRole($id){
        $this->startTrans();
        //删除角色表记录
        if($this->delete($id) === false){
            $this->rollback();
            return false;
        }
        //删除中间表记录
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['role_id'=>$id])->delete() === false){
            $this->error = '删除权限失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    
    /**
     * 获取角色及其权限.
     * @param type $id
     * @return type
     */
    public function getRoleInfo($id) {
        $row = $this->find($id);
        $row['permission_ids'] = json_encode(M('RolePermission')->where(['role_id'=>$id])->getField('permission_id',true));
        return $row;
    }
}
