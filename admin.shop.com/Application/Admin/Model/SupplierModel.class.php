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
class SupplierModel extends \Think\Model{
    
    protected $_validate = [
        ['name','require','供应商名称不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT],
        ['name','','供应商已存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT],
    ];
}
