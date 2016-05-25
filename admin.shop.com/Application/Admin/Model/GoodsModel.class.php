<?php

namespace Admin\Model;

class GoodsModel extends \Think\Model {
    
    /**
     * 商品促销状态
     * @var type 
     */
    public $goods_statuses = [
        1=>'精品',
        2=>'新品',
        4=>'热销',
    ];
    
    /**
     * 商品售卖状态.
     * @var type 
     */
    public $is_on_sales = [
        1=>'上架',
        0=>'下架',
    ];

    protected $_validate = [
        ['name', 'require', '商品名不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['goods_category_id', 'require', '商品分类不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['brand_id', 'require', '商品品牌不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['supplier_id', 'require', '供货商不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['shop_price', 'currency', '售价不合法', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['market_price', 'currency', '市场价不合法', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
    ];
    protected $_auto     = [
        ['inputtime', NOW_TIME, self::MODEL_INSERT],
        ['goods_status', 'array_sum', self::MODEL_BOTH, 'function'],
        ['sn', 'createSn', self::MODEL_INSERT, 'callback'],
    ];

    /**
     * 处理sn,如果传递了,以传递的为准,否则自动生成
     * 规则:SN年月日(6位数字)
     * @param string $sn 货号
     * @return string
     */
    protected function createSn($sn) {
        if ($sn) {
            return $sn;
        }
        //计算:SN20160513000001
        //获取当天的商品数量
        $goods_num_model = M('GoodsNums');
        $date            = date('Ymd');
        $num             = $goods_num_model->getFieldByDate($date, 'num');
        if ($num) {
            $num++;
            //将商品数量写入到数据表
            $goods_num_model->setField(['date' => $date, 'num' => $num]);
        } else {
            $num = 1;
            $goods_num_model->add(['date' => $date, 'num' => $num]);
        }

        $sn = 'SN' . $date . str_pad($num, 6, '0', STR_PAD_LEFT);
        return $sn;
    }

    /**
     * 1.保存商品基本信息
     * 2.保存商品详细描述
     * 3.相册
     */
    public function addGoods() {
        unset($this->data[$this->getPk()]);
        $this->startTrans();
        //添加商品的基本信息
        if (($goods_id = $this->_saveGoodsInfo()) === false) {
            $this->rollback();
            return false;
        }

        //保存商品详细描述
        if ($this->_saveGoodsIntro($goods_id) === false) {
            $this->rollback();
            return false;
        }

        //保存商品相册
        if ($this->_saveGoodsGallery($goods_id) === false) {
            $this->rollback();
            return false;
        }

        $this->commit();
        return true;
    }

    /**
     * 保存基本信息.
     * 可以执行添加可以执行修改
     * @param boolean $is_new 是否是新增商品.如果是true调用add否则save
     * @return boolean
     */
    private function _saveGoodsInfo($is_new = true) {
        if ($is_new) {
            if (($goods_id = $this->add()) === false) {
                return false;
            }
        } else {
            if (($goods_id = $this->save()) === false) {
                return false;
            }
        }
        return $goods_id;
    }

    /**
     * 保存商品详细描述.
     * @param integer $goods_id 商品id
     * @return boolean
     */
    private function _saveGoodsIntro($goods_id, $is_new = true) {
        $content_model = M('GoodsIntro');
        $data          = [
            'goods_id' => $goods_id,
            'content'  => I('post.content', '', false),
        ];
        if ($is_new) {
            if ($content_model->add($data) === false) {
                $this->error = $content_model->getError();
                return false;
            }
        } else {
            if ($content_model->save($data) === false) {
                $this->error = $content_model->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * 保存相册.
     * @param integer $goods_id 商品id.
     * @return boolean
     */
    private function _saveGoodsGallery($goods_id) {
        $gallery_model = M('GoodsGallery');
        $paths         = I('post.path');
        $data          = [];
        foreach ($paths as $path) {
            $data[] = [
                'goods_id' => $goods_id,
                'path'     => $path,
            ];
        }
        if ($data && $gallery_model->addAll($data) === false) {
            $this->error = $gallery_model->getError();
            return false;
        }
        return true;
    }

    /**
     * 获取分页列表
     * @param array $cond
     * @return type
     */
    public function getPageResult(array $cond = []) {
        $cond      = array_merge(['status' => 1], $cond);
        //分页列表
        $count     = $this->where($cond)->count();
        $size      = C('PAGE_SIZE');
        $page      = new \Think\Page($count, $size);
        $page->setConfig('theme', C('PAGE_THEME'));
        $page_html = $page->show();
        //获取当页数据
        $rows      = $this->where($cond)->page(I('get.p'), $size)->select();
        foreach ($rows as $key => $value) {
            $value['is_best'] = $value['goods_status'] & 1 ? 1 : 0;
            $value['is_new']  = $value['goods_status'] & 2 ? 1 : 0;
            $value['is_hot']  = $value['goods_status'] & 4 ? 1 : 0;
            $rows[$key]       = $value;
        }
        return ['rows' => $rows, 'page_html' => $page_html];
    }

    /**
     * 获取商品信息用于回显.
     * @param integer $id 商品id.
     * @return array
     */
    public function getGoodsInfo($id) {
        //1.获取基本信息
        $row           = $this->alias('g')->join('__GOODS_INTRO__ AS gi ON gi.goods_id=g.id')->find($id);
        $gallery_model = M('GoodsGallery');
        $row['paths']  = $gallery_model->where(['goods_id' => $id])->getField('id,path', true);
        return $row;
    }

    /**
     * 修改商品.
     * @return boolean
     */
    public function updateGoods() {
        $this->startTrans();
        $request_data = $this->data;
        //1.保存基本信息
        if ($this->_saveGoodsInfo(false) === false) {
            $this->rollback();
            return false;
        }
        //2.保存详细描述
        if ($this->_saveGoodsIntro($request_data['id'],false) === false) {
            $this->rollback();
            return false;
        }
        //3.保存相册信息
        if ($this->_saveGoodsGallery($request_data['id']) === false) {
            $this->rollback();
            return false;
        }
        
        //4.保存会员价格
        $member_prices = I('post.member_price');
        //4.1删除原有会员价格
        $member_price_model = M('MemberGoodsPrice');
        $member_price_model->where(['goods_id'=>$request_data['id']])->delete();
        $data = [];
        foreach($member_prices as $level=>$price){
            if(empty($price)){
                continue;
            }
            $data[] = [
                'goods_id'=>$request_data['id'],
                'member_level_id'=>$level,
                'price'=>$price,
            ];
        }
        //4.2保存商品的会员价格
        if($data){
            $member_price_model->addAll($data);
        }
        
        
        $this->commit();
        return true;
    }

}
