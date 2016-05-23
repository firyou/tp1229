<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Model;

/**
 * Description of LocationsModel
 *
 * @author qingf
 */
class LocationsModel extends \Think\Model{
    public function getListByParentId($parent_id=0) {
        return $this->where(['parent_id'=>$parent_id])->select();
    }
}
