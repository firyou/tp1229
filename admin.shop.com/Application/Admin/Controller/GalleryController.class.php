<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * Description of GalleryController
 *
 * @author qingf
 */
class GalleryController extends \Think\Controller{
    public function delete($id){
        $gallery_model = M('GoodsGallery');
        if($gallery_model->delete($id)===false){
            $this->error($gallery_model->getError());
        }else{
            $this->success('删除成功');
        }
    }
}
