<?php
// +---------------------------------------------------------------------+
// | MamiTianshi    | [ CREATE BY WF_RT TEAM ]                           |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Fjwcoder <fjwcoder@gmail.com>                          |
// +---------------------------------------------------------------------+
// | Repository | git@github.com:fjwcoder/mamitianshi_server.git         |
// +---------------------------------------------------------------------+

namespace app\index\logic;




class Script extends IndexBase 
{
    private $regist_code = 'SMS_109680031'; // 注册
    private $forget_pwd = 'SMS_109680030'; //修改密码验证码
    private $change_mobile = 'SMS_109680029'; //信息变更验证码
    public function sendSms(){
        $param = [
            'sign_name'=>'妈咪爱天使',
            'template_code'=>$this->change_mobile,
            'phone_number'=>18706681210,
            'template_param'=>[
                'code'=>123456,
                'product'=>'信息变更验证码'
            ]
        ];
        $response = $this->serviceSms->driverAlidy->sendSms($param);
        dump($response); die;
    }

    public function testcache(){
        $res = $this->modelApi->setCacheInfo()['api'];
        dump($res); die; 
    }
}
