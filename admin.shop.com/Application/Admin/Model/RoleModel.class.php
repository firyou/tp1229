<?php
namespace Admin\Model;

class RoleModel extends \Think\Model{
    /**
     * TODO:自动验证
     * name不能为空
     */
    
    /**
     * 获取分类列表
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
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach($permission_ids as $permission_id){
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        if(M('RolePermission')->addAll($data)===false){
            $this->error = '保存权限失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    
    /**
     * 修改分类.
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
        //删除原来的关联关系
        $role_permission_model = M('RolePermission');
        $role_permission_model->where(['role_id'=>$request_data['id']])->delete();
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach($permission_ids as $permission_id){
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        if(M('RolePermission')->addAll($data)===false){
            $this->error = '保存权限失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    
    /**
     * 使用物理删除方式删除商品分类,会同时删除后代分类.
     * @param integer $id 当前分类id.
     * @return type
     */
    public function deleteRole($id){
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
