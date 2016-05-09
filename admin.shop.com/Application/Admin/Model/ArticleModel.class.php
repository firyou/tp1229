<?php

namespace Admin\Model;

/**
 * Description of BrandModel
 *
 * @author qingf
 */
class ArticleModel extends \Think\Model {
    
    protected $_validate = [
        ['name','require','文章名称不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
        ['article_category_id','require','文章分类不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
    ];
    
    protected $_auto = [
        ['inputtime',NOW_TIME,self::MODEL_INSERT],
    ];

    /**
     * 获取分页结果集.
     * @param array $cond 查询条件.
     * @return array
     */
    public function getPageResult(array $cond = array()) {
        //获取总行数
        $cond = array_merge(['status'=>['gt',0]],$cond);
        $count = $this->where($cond)->count();
        $size = C('PAGE_SIZE');
        //获取分页代码
        //>>创建分页对象
        $page = new \Think\Page($count,$size);
        //>>配置分页样式
        $page->setConfig('theme', C('PAGE_THEME'));
        //>>获取分页代码
        $page_html = $page->show();
        //获取当前页数据
        $rows = $this->where($cond)->page(I('get.p'),$size)->select();
        //返回分页代码和结果集
        return ['page_html'=>$page_html,'rows'=>$rows];
    }
    
    /**
     * 添加文章.包括详细内容.
     * @return boolean
     */
    public function addArticle(){
        if(($article_id = $this->add())===false){
            return false;
        }
        //创建详情表的模型对象
        $article_content_model = M('ArticleContent');
        //准备数据
        $data = [
            'article_id'=>$article_id,
            'content'=>I('post.content'),
        ];
        if($article_content_model->add($data)===false){
            $this->error = $article_content_model->getError();
            return false;
        }
        return true;
    }
    
    public function getArticleInfo($id) {
        //select * from article as a inner join article_content as ac on a.article_category_id=ac.id
        $row = $this->alias('a')->join('__ARTICLE_CONTENT__ as ac ON a.id=ac.article_id')->find($id);
        if(empty($row)){
            $this->error = '文章不存在,请检查后再试';
            return false;
        }
        return $row;
    }
    
    /**
     * 编辑文章
     * @return boolean
     */
    public function updateArticle() {
        $request_data = $this->data;
        if($this->save()===false){
            return false;
        }
        //创建详情表的模型对象
        $article_content_model = M('ArticleContent');
        //准备数据
        $data = [
            'article_id'=>$request_data['id'],
            'content'=>I('post.content'),
        ];
        
        if($article_content_model->save($data)===false){
            $this->error = $article_content_model->getError();
            return false;
        }
        return true;
    }

}
