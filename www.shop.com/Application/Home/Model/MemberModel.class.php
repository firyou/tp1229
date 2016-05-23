<?php
namespace Home\Model;
class MemberModel extends \Think\Model{
    /**
     * 注册时
     * username 必填 唯一  3-20位  
     * password 必填 6-20位
     * repassword 和password必须一致
     * email 必填 email格式
     * tel 必填 满足中国手机号码规则
     * todo:: tel_code 必填 自定义验证规则(和发送的短信内容匹配)
     * img_code 必填 使用自定义验证规则
     * agree 必填
     * @var type
     */
    protected $_validate = [
        ['username','require','用户名不能为空',self::MUST_VALIDATE,'','reg'],
        ['username','','用户名已存在',self::MUST_VALIDATE,'unique','reg'],
        ['username','3,20','用户名长度不合法',self::MUST_VALIDATE,'length','reg'],
        ['password','require','密码不能为空',self::MUST_VALIDATE,'','reg'],
        ['password','6,20','密码长度不合法',self::MUST_VALIDATE,'length','reg'],
        ['repassword','password','两次密码不一致',self::MUST_VALIDATE,'confirm','reg'],
        ['email','email','邮箱不合法',self::MUST_VALIDATE,'','reg'],
        ['tel','/^(13|14|15|17|18)\d{9}$/','手机号码不合法',self::MUST_VALIDATE,'regex','reg'],
//        ['img_code','check_img_code','验证码不正确',self::MUST_VALIDATE,'callback','reg'],
        ['tel_code','check_tel_code','验证码不正确',self::MUST_VALIDATE,'callback','reg'],
        ['agree','require','必须同意许可协议',self::MUST_VALIDATE,'','reg'],
        
        ['username','require','用户名不能为空',self::MUST_VALIDATE,'','login'],
        ['password','require','密码不能为空',self::MUST_VALIDATE,'','login'],
//        ['img_code','check_img_code','验证码不正确',self::MUST_VALIDATE,'callback','login'],
        
    ];
    
    /**
     * 自动完成
     * 注册时间  随机盐 初始状态
     * @var type 
     */
    protected $_auto = [
        ['add_time',NOW_TIME,'reg'],
        ['salt','\Org\Util\String::randString','reg','function',6],
        ['status',0,'reg'],
    ];
    
    /**
     * 验证图片验证码
     * @return boolean
     */
    protected function check_img_code($code) {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
    
    /**
     * 验证用户填写的手机验证码是否匹配
     * @param string $code 用户提交的验证码.
     * @return boolean
     */
    protected function check_tel_code($code) {
        $session_code = session('REG_CODE');
        session('REG_CODE',null);//用完了,销毁掉
        if($session_code==$code){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 用户注册
     * TODO:用户注册完成发送邮件到用户提交的邮箱中,必须点击链接才能激活账号.
     * @return type
     */
    public function addMember() {
        //将密码加盐加密
        $this->data['password'] = salt_mcrypt($this->data['password'], $this->data['salt']);
        //验证手机验证码是否匹配
        $code = session('REG_CODE');
        return $this->add();
    }
    
    /**
     * 用户登录
     */
    public function login() {
        //获取用户信息
        $request_data = $this->data;
        $user_info = $this->getByUsername($request_data['username']);
        if(!$user_info){
            $this->error = '用户不存在';
            return false;
        }
        $password =  salt_mcrypt($request_data['password'], $user_info['salt']);
        if($password != $user_info['password']){
            $this->error = '密码错误';
            return false;
        }
        
        //保存session信息
        login($user_info);
        
        //登陆成功将cookie中的购物车数据保存到数据库中
        D('Cart')->cookie2Db();
        
        //TODO::自动登陆
        return true;
    }
}
