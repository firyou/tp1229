<?php

namespace Admin\Controller;

class ArticleController extends \Think\Controller {

    /**
     * @var \Admin\Model\ArticleModel 
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('Article');
        $meta_titles  = [
            'index' => '管理文章',
            'add'   => '添加文章',
            'edit'  => '修改文章',
        ];
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理文章';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * [
     *    'id'=>'name'
     * '1'=>'购物流程',
     * '2'=>'配送方式',
     * ]
     */
    public function index() {
        //读取分页数据
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = ['like', '%' . $name . '%'];
        }
        $this->assign($this->_model->getPageResult($cond));
        //获取所有的文章分类
        $article_category_model = D('ArticleCategory');
        $article_categories = $article_category_model->getList('id,name');
        $this->assign('article_categories', $article_categories);
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //添加到数据表
            if ($this->_model->addArticle() === false) {
                $this->error($this->_model->getError());
            }
            //提示跳转
            $this->success('添加成功', U('index', ['nocache' => NOW_TIME]));
        } else {
            //准备分类数据
            $article_category_model = D('ArticleCategory');
            $this->assign('article_categories', $article_category_model->getList());
            $this->display();
        }
    }

    /**
     * 编辑文章
     * @param type $id
     */
    public function edit($id) {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //添加到数据表
            if ($this->_model->updateArticle() === false) {
                $this->error($this->_model->getError());
            }
            //提示跳转
            $this->success('修改成功', U('index', ['nocache' => NOW_TIME]));
        } else {
            //获取数据
            if (($row = $this->_model->getArticleInfo($id)) === false) {
                $this->error($this->_model->getError());
            }
            //传递数据
            $this->assign('row', $row);
            //准备分类数据
            $article_category_model = D('ArticleCategory');
            $this->assign('article_categories', $article_category_model->getList());
            //加载视图
            $this->display('add');
        }
    }

    /**
     * 删除文章
     * @param integer $id 文章id.
     */
    public function delete($id) {
        if ($this->_model->where(['id' => $id])->setField('status', 0) === false) {
            $this->error($this->_model->getError());
        } else {
            //提示跳转
            $this->success('修改成功', U('index', ['nocache' => NOW_TIME]));
        }
    }

}
