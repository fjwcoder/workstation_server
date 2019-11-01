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

namespace app\api\controller;

/**
 * 公共基础接口控制器
 */
class Common extends ApiBase
{

    /**
     * 接种台叫号
     */
    public function callName()
    {

        return $this->logicCommon->callName($this->param);

    }

    /**
     * 冰箱用户登录
     * add by fqm in 19.10.30
     */
    public function userLogin()
    {
        return $this->apiReturn($this->logicCommon->userLogin($this->param));
    }






    /**+------------------------
     * | 前台用户操作接口
     * |
     * +------------------------
     */
    
    /**
     * 前台用户获取RSA公钥+RSA加密方式的AES秘钥
     * 调用接口者使用RSA公钥解密获取AES秘钥明文
     * add fqm 19.8.21
     */
    public function encryptKey()
    {
        return $this->apiReturn($this->logicCommon->encryptKey($this->param));
    }


    /**
     * 操作人员登录
     * add fqm 19.8.22
     */
    public function operatorLogin()
    {
        return $this->apiReturn($this->logicCommon->operatorLogin($this->param));
    }


    /**+------------------------
     * | 后台用户操作接口
     * |
     * +------------------------
     */
    
    /**
     * 后台用户登录接口
     */
    public function member_login()
    {
        
        return $this->apiReturn($this->logicCommon->member_login($this->param));
    }
    
    /**
     * 后台用户修改密码接口
     */
    public function member_changePassword()
    {
        
        return $this->apiReturn($this->logicCommon->member_changePassword($this->param));
    }
    
    /**
     * 友情链接
     */
    public function getBlogrollList()
    {
        
        return $this->apiReturn($this->logicCommon->getBlogrollList($this->param));
    }
}
