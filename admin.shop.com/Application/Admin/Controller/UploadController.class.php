<?php

namespace Admin\Controller;

class UploadController extends \Think\Controller {

    //执行上传操作.
    public function upload() {
        //1.创建upload对象
        $options    = C('UPLOAD_SETTING');
        //2.执行对象的配置
        $upload     = new \Think\Upload($options);
        //3.执行上传操作
        //3.1使用upload上传,因为使用uploadone需要知道控件名字
        $file_infos = $upload->upload();
        //3.2由于每次请求都只会上传一个文件,所以我们可以大胆使用array_shift返回第一个文件信息
        //4.获取上传的结果
        if ($file_infos) {
            $file_info = array_shift($file_infos);
            $root_path = str_replace(SITE_ROOT, '', $options['rootPath']);
            if($upload->driver == 'Qiniu'){
                $file_url = $file_info['url'];
            } else{
                $base_url  = BASE_URL;
                $file_url = $base_url . $root_path . $file_info['savepath'] . $file_info['savename'];
            }
            $return    = [
                'status'   => 1,
                'msg'      => '',
                'file_url' => $file_url,
            ];
        } else {
            $return = [
                'status'   => 0,
                'msg'      => $upload->getError(),
                'file_url' => '',
            ];
        }
        //5.返回上传结果(json)
        $this->ajaxReturn($return);
    }

}
