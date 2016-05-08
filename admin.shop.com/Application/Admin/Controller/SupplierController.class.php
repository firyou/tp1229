<?php

/**
 * 供应商的控制器文件.
 * @author kunx-edu<kunx-edu@qq.com>
 */

namespace Admin\Controller;

class SupplierController extends \Think\Controller {
    
    /**
     * @var \Admin\Model\SupplierModel
     */
    private $_model = null;
    
    protected function _initialize() {
        // 由于Supplier模型在此控制器中经常调用,所以初始化控制器的时候存放到一个属性中.
        $this->_model = D('Supplier');
        // 设定标题.
        $meta_titles = [
            'index'=>'管理供货商',
            'add'=>'添加供货商',
            'edit'=>'修改供货商',
        ];
        $meta_title = isset($meta_titles[ACTION_NAME])?$meta_titles[ACTION_NAME]:'管理供货商';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 列表页面.
     */
    public function index() {
        //1.获取列表
        //1.1创建模型
        //1.2调用模型中的查询代码
        //1.3获取查询条件,并传递给模型
        $name = I('get.name');
        $cond = [];
        if($name){
            $cond['name'] = ['like',  '%' . $name . '%'];
        }
        $rows = $this->_model->getPageResult($cond);
        //1.3分配数据
        $this->assign($rows);
//        $this->assign('meta_title', '管理供货商');
        $this->display();
    }

    /**
     * 添加供应商.
     */
    public function add() {
        if (IS_POST) {
            //1.实例化对象
            //2.收集数据
            if($this->_model->create()===false){
                $this->error($this->_model->getError());
            }
            //3.执行添加操作
            if($this->_model->add()===false){
                $this->error($this->_model->getError());
            }
            //4.提示跳转
            $this->success('添加成功',U('index'));
        } else {
//            $this->assign('meta_title', '添加供货商');
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
