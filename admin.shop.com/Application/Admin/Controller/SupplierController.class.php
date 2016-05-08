<?php

/**
 * 供应商的控制器文件.
 * @author kunx-edu<kunx-edu@qq.com>
 */

namespace Admin\Controller;

class SupplierController extends \Think\Controller {

    /**
     * 列表页面.
     */
    public function index() {
        
    }

    /**
     * 添加供应商.
     */
    public function add() {
        if (IS_POST) {
            //1.实例化对象
            $model = D('Supplier');
            //2.收集数据
            if($model->create()===false){
                $this->error($model->getError());
            }
            //3.执行添加操作
            if($model->add()===false){
                $this->error($model->getError());
            }
            //4.提示跳转
            $this->success('添加成功',U('index'));
        } else {
            $this->display();
        }
    }

    /**
     * 修改供应商
     * @param integer $id 要修改的供应商id.
     */
    public function edit($id) {
        
    }

}
