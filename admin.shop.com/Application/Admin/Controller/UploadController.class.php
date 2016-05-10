<?php

namespace Admin\Controller;

class UploadController extends \Think\Controller {

    //执行上传操作.
    public function upload() {
        //1.创建upload对象
        $options = [
            'mimes'        => array('image/jpeg', 'image/png', 'image/gif'), //允许上传的文件MiMe类型
            'maxSize'      => 3145728, //上传的文件大小限制 (0-不做限制)
            'exts'         => array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
//            'exts'         => array('kunx'), //允许上传的文件后缀
            'autoSub'      => true, //自动子目录保存文件
            'subName'      => array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath'     => SITE_ROOT . '/Uploads/', //保存根路径
            'savePath'     => '', //保存路径
            'saveName'     => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt'      => '', //文件保存后缀，空则使用原后缀
            'replace'      => false, //存在同名是否覆盖
            'hash'         => true, //是否生成hash编码
            'callback'     => false, //检测文件是否存在回调，如果存在返回文件信息数组
            'driver'       => '', // 文件上传驱动
            'driverConfig' => array(), // 上传驱动配置
        ];
        //2.执行对象的配置
        $upload  = new \Think\Upload($options);
        //3.执行上传操作
        //3.1使用upload上传,因为使用uploadone需要知道控件名字
        $file_infos = $upload->upload();
        //3.2由于每次请求都只会上传一个文件,所以我们可以大胆使用array_shift返回第一个文件信息
        //4.获取上传的结果
        if($file_infos){
            $file_info = array_shift($file_infos);
            $root_path = str_replace(SITE_ROOT, '', $options['rootPath']);
            $base_url = BASE_URL;
            $return = [
                'status'=>1,
                'msg'=>'',
                'file_url'=>  $base_url . $root_path.$file_info['savepath'].$file_info['savename'],
            ];
        }else{
            $return = [
                'status'=>0,
                'msg'=>$upload->getError(),
                'file_url'=>'',
            ];
        }
        //5.返回上传结果(json)
        $this->ajaxReturn($return);
    }

}
