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

/**
 * 发送短信
 * @param string $telphone 手机号码 如果是多个手机号码,使用英文逗号分开
 * @param string $data 短信模板所需要的参数json字符串
 * @param string $sign 签名.
 * @param string $tmplate 短信模板标识.
 * @return boolean
 */
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
    
    /**
     * 发送邮件
     * @param array|string $address 收件人地址
     * @param string $subject 标题
     * @param string $content 内容
     * @return type
     */
    function sendmail($address,$subject,$content){
        //获取配置
        $setting = C('EMAIL_SETTING');
        //加载自动载入类库
        vendor('PHPMailer.PHPMailerAutoload');
        //创建发送邮件的对象
        $mail = new \PHPMailer;
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host     = $setting['host'];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $setting['username'];                 // SMTP username
        $mail->Password = $setting['password'];                           // SMTP password
        $mail->SMTPSecure =$setting['SMTPSecure'];                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port     = $setting['port'];                                    // TCP port to connect to

        $mail->setFrom($setting['username']);
        //如果是数组,就批量发送
        if(is_array($address)){
            foreach($address as $item){
                $mail->addAddress($item);     // Add a recipient
            }
        }else{
            $mail->addAddress($address);     // Add a recipient
        }

        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->CharSet = 'utf-8';
        $mail->Subject = $subject;
        $mail->Body    = $content;
        return $mail->send();
    }