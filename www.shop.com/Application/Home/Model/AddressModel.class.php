<?php

namespace Home\Model;

class AddressModel extends \Think\Model{
    
    /**
     * 添加地址.
     */
    public function addAddress() {
        $userinfo = login();
        unset($this->data[$this->getPk()]);
        $this->data['member_id'] = $userinfo['id'];
        //如果勾选了默认,就清空其它默认,然后再添加
        if(isset($this->data['is_default'])){
            $this->where(['member_id'=>$userinfo['id']])->setField('is_default',0);
        }
        return $this->add();
    }
    
    /**
     * 获取当前用户的所有地址.
     * @return type
     */
    public function getList() {
        $userinfo = login();
        return $this->where(['member_id'=>$userinfo['id']])->select();
    }
    
    /**
     * 获取指定id的详细信息.
     * @param integer $id
     * @return type
     */
    public function getAddressInfo($id) {
        $userinfo = login();
        return $this->where(['member_id'=>$userinfo['id'],'id'=>$id])->find();
    }
    
    /**
     * 修改收货地址.
     * @return type
     */
    public function saveAddress() {
        $userinfo = login();
        $this->data['member_id'] = $userinfo['id'];
        //如果勾选了默认,就清空其它默认,然后再添加
        if(isset($this->data['is_default'])){
            $this->where(['member_id'=>$userinfo['id']])->setField('is_default',0);
        }
        return $this->save();
    }
}
