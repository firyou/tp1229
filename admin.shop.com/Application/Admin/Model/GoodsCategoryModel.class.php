<?php
namespace Admin\Model;

class GoodsCategoryModel extends \Think\Model{
    /**
     * TODO:自动验证
     * name不能为空
     */
    
    /**
     * 获取分类列表
     */
    public function getList(){
        return $this->where(['status'=>1])->order('lft')->select();
    }
    
    public function addCategory(){
        //实例化一个nestedsets所需要的数据库操作类的对象
//        $orm = new \Admin\Model\NestedSetsMysql();
        //使用D函数创建对象,第一个实参是模型类名,第二个是模型层标示(也就是模型文件夹),注意,模型文件根据规范都应当以层的标识为结束字符串
        $orm = D('NestedSetsMysql','Logic');
        //创建一个nestedsets对象
        $nestedsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        if(($cat_id = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom'))===false){
            $this->error = M()->getError();
            return false;
        }else{
            return $cat_id;
        }
    }
    
    /**
     * 修改分类.
     * @return boolean
     */
    public function updateCategory() {
        //先判断是否更改了父级分类:先获取数据表中原有的还有当前提交的
        $parent_id = $this->getFieldById($this->data['id'],'parent_id');
        //由于nestedsets在不修改层级的时候会导致执行结果为false,所以需要先判断一下是否需要移动层级
        if($parent_id != $this->data['parent_id'] ){
            //实例化nestedsets所需的orm对象
            $orm = D('NestedSetsMysql','Logic');
            //创建一个nestedsets对象
            $nestedsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
            if($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false){
                $this->error = '父级分类不正确';
                return false;
            }
        }
        return $this->save();
    }
    
    /**
     * 使用物理删除方式删除商品分类,会同时删除后代分类.
     * @param integer $id 当前分类id.
     * @return type
     */
    public function deleteCategory($id){
        //逻辑删除
        //1.获取当前分类的左右边界
//        $info = $this->where(['id'=>$id])->field('lft,rght')->find();
//        $cond = [
//            'lft'=>['egt',$info['lft']],
//            'rght'=>['elt',$info['rght']],
//        ];
//        $this->where($cond)->save(['status'=>0]);
//        var_dump($cond);
//        return;
        
        //物理删除.
        //实例化nestedsets所需的orm对象
        $orm = D('NestedSetsMysql','Logic');
        //创建一个nestedsets对象
        $nestedsets = new \Admin\Service\NestedSets($orm, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        return $nestedsets->delete($id);
    }
}
