<?php

/**
 * 加盐加密
 * @param string $password 原始密码
 * @param string $salt     盐
 * @return string
 */
function salt_mcrypt($password,$salt){
    return md5(md5($password) . $salt);
}

/**
 * 保存/获取用户session信息.
 * @param array $data
 * @return null|array
 */
function login($data = null){
    if($data){
        session('USER_INFO',$data);
    }else{
        return session('USER_INFO');
    }
}


function sendSms($telphone,$data,$sign='四哥测试',$tmplate='SMS_5590023') {
        //引入阿里大鱼的自动加载机制文件
        vendor('Alidayu.Autoloader');
        date_default_timezone_set('Asia/Shanghai');
        $c            = new \TopClient;
        $c->format='json';
        $c->appkey    = '23368733';
        $c->secretKey = '260d2fbb9cb7108421d096e6a6201853';
        $req          = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($sign);
        $code = (string)mt_rand(1000, 9999);
        $req->setSmsParam(json_encode($data));
        $req->setRecNum($telphone);
        $req->setSmsTemplateCode($tmplate);
        $resp         = $c->execute($req);
        if(isset($resp->result) && isset($resp->result->success)){
            $status = true;
        }else{
            $status = false;
        }
        return $status;
    }