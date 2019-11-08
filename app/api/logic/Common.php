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

namespace app\api\logic;

use app\api\error\CodeBase;
use app\api\error\Common as CommonError;
use \Firebase\JWT\JWT;
use think\Db;
/**
 * 接口基础逻辑
 */
class Common extends ApiBase
{



    /**
     * 接种台叫号
     */
    public function callName($param = [])
    {

        $data = [
            'deviceId'=>(int)$param['deviceId'],
            'data' =>[
                'number'=>$param['number'],
                'childName'=>$param['childName'],
                'consultingRoom'=>$param['consultingRoom']
            ]
        ];

        $url = $this->modelSettings->getValue(['Name'=>'App.QueueServerAddress'],'Value');

        $result = httpsPost($url, json_encode($data));

        return $result;
    }

    /**
     * 冰箱用户登录
     * add by fqm in 19.10.30
     */
    public function userLogin($param = [])
    {
        
        if(empty($param['account'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '请填写账号'];
        if(empty($param['password'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '请填写密码'];
        
        $user_info = $this->modelUsers->getInfo(['UserName'=>$param['account']], 'Id, UserName, md5_password');

        if($user_info){

            $group_id = Db::name('auth_group_access')->where('member_id',$user_info['Id'])->value('group_id');

            if($group_id !== 1 && $group_id !== 3){
                return [API_CODE_NAME => 40001, API_MSG_NAME => '没有权限'];
            }

            if(md5($param['password']) !== $user_info['md5_password']){
                return [API_CODE_NAME => 40001, API_MSG_NAME => '密码错误'];
            }else{
                return $user_info;
            } 

        }else{
            return [API_CODE_NAME => 40001, API_MSG_NAME => '账号不存在'];
        }
        






        
    }
    

    /**+------------------------
     * | 前台用户操作接口
     * |
     * +------------------------
     */

     /**
     * 前台用户 JWT验签方法
     */
    public static function user_tokenSign($user)
    {
        
        $key = API_KEY . JWT_KEY;
        
        $jwt_data = ['user_id' => $user['id'], 'create_time' => time()];
        
        $token = [
            "iss"   => "MamiTianshi JWT",         // 签发者
            "iat"   => TIME_NOW,              // 签发时间
            "exp"   => TIME_NOW + TIME_NOW,   // 过期时间
            "aud"   => 'MamiTianshi',             // 接收方
            "sub"   => 'MamiTianshi',             // 面向的用户
            "data"  => $jwt_data
        ];
        
        $jwt = JWT::encode($token, $key);
        
        $jwt_data['user_token'] = $jwt;
        
        return $jwt_data;
    }


    /**
     * add fqm 19.8.21
     * 获取RSA公钥+RSA加密方式的AES秘钥
     */
    public function encryptKey($param = [])
    {
        // AES秘钥明文
        $_aes_key = aes_key();

        // AES秘钥使用RSA私钥加密
        $aes_key = rsa_encrypt($_aes_key, 'pri');

        // RSA公钥
        $rsa_pub_file = rsa_file('pub');
        // 读取RSA公钥
        $handle = fopen($rsa_pub_file, "r");
        $content = fread($handle, filesize ($rsa_pub_file));

        // 去掉公钥中的 换行 开头 结尾
        $content = str_replace(array("\n","\r"),"",$content); 
        $content = str_replace("-----BEGIN PUBLIC KEY-----","",$content);
        $rsa_pub = str_replace("-----END PUBLIC KEY-----","",$content);

        $data = [
            'rsa_pub' => $rsa_pub,
            'aes_key' => $aes_key,
        ];

        return $data;
    }

    /**
     * 操作人员登录
     * edit by fqm in 19.9.2 缩短部分传输字段键名
     */
    public function operatorLogin($param = [])
    {

        $login_type = isset($param['login_type']) ? $param['login_type'] : 1;

        $unique_code = rsa_decrypt($param['uc'],'pub');

        $position_id = $this->modelMPositionFridge->getValue(['unique_code'=>$unique_code],'position_id');
        
        // 判断登录方式 1：账号密码 2：指纹信息 3：面部识别
        if(trim($login_type) === '1'){
            $account = !empty($param['account']) ? $param['account'] : '';
            $password = !empty($param['pwd']) ? $param['pwd'] : '';
            return $this->verifyAccPwd($account, $password, $position_id);
        }


    }

    /**
     * 用户使用账号密码登录 验证账号密码
     */
    public function verifyAccPwd($account, $password, $position_id)
    {
        
        $where = [
            'position_id' => $position_id,
            'mobile' => trim($account),
        ];

        $field = 'id, position_id, fridge_id, user_type, name, mobile, password';

        $userInfo = $this->modelMPositionOperator->getInfo($where, $field);

        if(!$userInfo){
            return [API_CODE_NAME => 40002, API_MSG_NAME => '账号不存在'];
        }
        
        if(md5(trim($password)) !== $userInfo['password']){
            return [API_CODE_NAME => 40003, API_MSG_NAME => '密码不正确'];
        }



        return $userInfo;
    }
    

    /**+------------------------
     * | 后台用户操作接口
     * |
     * +------------------------
     */

    /**
     * 后台用户登录接口逻辑
     */
    public function member_login($data = [])
    {

        $validate_result = $this->validateMember->scene('login')->check($data);
        
        if (!$validate_result) {
            
            return CommonError::$usernameOrPasswordEmpty;
        }
        
        begin:
        
        $member = $this->logicMember->getMemberInfo(['username' => $data['username']]);

        // 若不存在用户则注册
        if (empty($member))
        {
            $register_result = $this->member_register($data);
            
            if (!$register_result) {
                
                return CommonError::$registerFail;
            }
            
            goto begin;
        }
        
        if (data_md5_key($data['password']) !== $member['password']) {
            
            return CommonError::$passwordError;
        }
        
        return $this->member_tokenSign($member);
    }
    
    /**
     * 后台用户注册方法
     */
    public function member_register($data)
    {
        
        $data['nickname']  = $data['username'];
        $data['password']  = data_md5_key($data['password']);

        return $this->logicMember->setInfo($data);
    }
    
    /**
     * 后台用户 JWT验签方法
     */
    public static function member_tokenSign($member)
    {
        
        $key = API_KEY . JWT_KEY;
        
        $jwt_data = ['member_id' => $member['id'], 'nickname' => $member['nickname'], 'username' => $member['username'], 'create_time' => $member['create_time']];

        $token = [
            "iss"   => "MamiTianshi JWT",         // 签发者
            "iat"   => TIME_NOW,              // 签发时间
            "exp"   => TIME_NOW + TIME_NOW,   // 过期时间
            "aud"   => 'MamiTianshi',             // 接收方
            "sub"   => 'MamiTianshi',             // 面向的用户
            "data"  => $jwt_data
        ];
        
        $jwt = JWT::encode($token, $key);
        
        $jwt_data['user_token'] = $jwt;
        
        return $jwt_data;
    }
    
    /**
     * 修改密码
     */
    public function member_changePassword($data)
    {
        
        $member = get_member_by_token($data['user_token']);
        
        $member_info = $this->logicMember->getMemberInfo(['id' => $member->member_id]);
        
        if (empty($data['old_password']) || empty($data['new_password'])) {
            
            return CommonError::$oldOrNewPassword;
        }
        
        if (data_md5_key($data['old_password']) !== $member_info['password']) {
            
            return CommonError::$passwordError;
        }

        $member_info['password'] = $data['new_password'];
        
        $result = $this->logicMember->setInfo($member_info);
        
        return $result ? CodeBase::$success : CommonError::$changePasswordFail;
    }
    
    /**
     * 友情链接
     */
    public function getBlogrollList()
    {
        
        return $this->modelBlogroll->getList([DATA_STATUS_NAME => DATA_NORMAL], true, 'sort desc,id asc', false);
    }
}
