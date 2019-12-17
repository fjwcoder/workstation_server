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
use think\Request;
/**
 * 公共基础接口控制器
 */
class Common extends ApiBase
{

    /**
     * 获取播放视频列表
     */
    public function getVideoFiles()
    {

        // public下video文件夹
        $file_path = ROOT_PATH . 'video';

        $filename = scandir($file_path);

        $request = Request::instance();

        $requestIp = $request->ip();
        // 定义一个数组接收文件名
        $conname = array();
        foreach($filename as $k=>$v){
            // 跳过两个特殊目录   continue跳出循环
            if($v=="." || $v==".."){continue;}
            // 跳过后缀不是mp4的目录
            if(strpos($v,'mp4') == false && strpos($v,'MP4') == false){
                continue;
            }
            $conname[] = 'http://'.$requestIp.':21022/video/' . $v;
        }
        if(!empty($conname)){

            $data = ['code'=>200,'data'=>$conname];
        }else{
            
            $data = ['code'=>400,'msg'=>'请先保存视频'];
        }
        

        return json_encode($data,320);

    }

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
