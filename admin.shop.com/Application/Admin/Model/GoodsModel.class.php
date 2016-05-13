<?php

namespace Admin\Model;

class GoodsModel extends \Think\Model {

    protected $_validate = [
        ['name','require','商品名不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
        ['goods_category_id','require','商品分类不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
        ['brand_id','require','商品品牌不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
        ['supplier_id','require','供货商不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
        ['shop_price','currency','售价不合法',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
        ['market_price','currency','市场价不合法',self::EXISTS_VALIDATE,'',self::MODEL_BOTH],
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

        $sn = 'SN' . $date .str_pad($num,6,'0',STR_PAD_LEFT);
        return $sn;
    }

    /**
     * 1.保存商品基本信息
     * 2.保存商品详细描述
     * 3.TODO相册
     */
    public function addGoods() {
        $this->startTrans();
        if (($goods_id = $this->add()) === false) {
            $this->rollback();
            return false;
        }
        //保存商品详细描述
        $content_model = M('GoodsIntro');
        $data = [
            'goods_id'=>$goods_id,
            'content'=>I('post.content','',false),
        ];
        if($content_model->add($data)===false){
            $this->error = $content_model->getError();
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

}
