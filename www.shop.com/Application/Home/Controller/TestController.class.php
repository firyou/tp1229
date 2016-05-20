<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of TestController
 *
 * @author qingf
 */
class TestController extends \Think\Controller {

    public function sendMail() {
//        vendor('PHPMailer.PHPMailerAutoload');
//        $mail = new \PHPMailer;
//
//        $mail->isSMTP();                                      // Set mailer to use SMTP
////        $mail->Host     = 'smtp.126.com';  // Specify main and backup SMTP servers
////        $mail->SMTPAuth = true;                               // Enable SMTP authentication
////        $mail->Username = 'kunx_edu@126.com';                 // SMTP username
////        $mail->Password = 'iam4ge';                           // SMTP password
//////        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
////        $mail->Port     = 25;                                    // TCP port to connect to
//        $mail->Host     = 'smtp.exmail.qq.com';  // Specify main and backup SMTP servers
//        $mail->SMTPAuth = true;                               // Enable SMTP authentication
//        $mail->Username = 'tp1229@huizhizhongxiang.cn';                 // SMTP username
//        $mail->Password = 'Tp1229';                           // SMTP password
//        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
//        $mail->Port     = 465;                                    // TCP port to connect to
//
//        $mail->setFrom('tp1229@huizhizhongxiang.cn', 'sige');
//        $mail->addAddress('kunx_edu@126.com', 'mr quinn');     // Add a recipient
//        $mail->addAddress('775252280@qq.com', 'wu wang');     // Add a recipient
//        $mail->addAddress('heaszahu@sharklasers.com');     // Add a recipient
//
//        $mail->isHTML(true);                                  // Set email format to HTML
//        $mail->CharSet = 'utf-8';
//        $mail->Subject = 'hello good man';
//        $mail->Body    = '好男人<font color="red">四哥</font>';
//        if (!$mail->send()) {
//            echo 'Message could not be sent.';
//            echo 'Mailer Error: ' . $mail->ErrorInfo;
//        } else {
//            echo 'Message has been sent';
//        }
        if(sendmail('kunx-edu@qq.com', 'test send email', '520节日快乐')){
            echo '发送成功';
        }else{
            echo '发送失败';
        }
    }

}
