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

use app\common\logic\LogicBase;
use app\api\error\CodeBase;

/**
 * Api基础逻辑
 */
class ApiBase extends LogicBase
{

    /*
     * 白名单不验证token 如果传入token执行验证获取信息，没有获取到用户信息
     * */
    public static function whiteList()
    {
        return [
            // 'vaccine/scanappointmentqrcode',
           'vaccinations/nextnumber'
        ];
    }

    /**
     * API返回数据
     * change by fjw in 19.7.30
     * 加入加密参数
     * @param code_data 返回的数据
     * @param encode_type 加密方法，默认不加密
     * @param return_data 数据查询错误时，自定义的返回参数
     * @param return_type 返回参数格式
     */
    public function apiReturn($code_data = [], $return_data = [], $return_type = 'json')
    {
        // 查询接口信息
        $info = $this->modelApi->getCacheInfo(URL);

        if (is_array($code_data) && array_key_exists(API_CODE_NAME, $code_data)) {  // 如果返回失败  code != 200 走这里
            
            !empty($return_data) && $code_data['data'] = $return_data;

            $result = $code_data;
            
        } else {  // 如果返回成功 code == 200 走这里

            $result = CodeBase::$success;
            
            // edit by fjw in 19.7.30：由于rsa加密存在明文长度限制，所以只对返回数据中的data进行加密
            switch($info['encode_type']){
                case 'rsa':
                    $result['data'] = rsa_encrypt(json_encode($code_data, 320));
                break;
                case 'aes':
                    $result['data'] = aes_encrypt(json_encode($code_data, 320));
                break;
                default: 
                    $result['data'] = $code_data;
                break;
            }

        }

        $return_result = $this->checkDataSign($result, $info);
        
        $return_result['exe_time'] = debug('api_begin', 'api_end');

        return $return_type == 'json' ? json($return_result) : $return_result;

        
    }

    /**
     * 检查是否需要响应数据签名
     */
    public function checkDataSign($data, $info = [])
    {
        
        // $info = empty($info)?$this->modelApi->getCacheInfo(URL):$info; // 注释 by fjw in 19.7.31: 不加这一段判断，会提高一点执行效率
        
        $info['is_response_sign'] && !empty($data['data']) && $data['data']['data_sign'] = create_sign_filter($data['data']);
        
        return $data;
    }
    
    /**
     * API错误终止程序
     */
    public function apiError($code_data = [])
    {
        
        return throw_response_exception($code_data);
    }

    /**
     * API提交附加参数检查
     */
    public function checkParam($param = [])
    {
        // $info = $this->modelApi->getInfo(['api_url' => URL]);
        $info = $this->modelApi->getCacheInfo(URL);

        empty($info) && $this->apiError(CodeBase::$apiUrlError);

        /**
         * edit by fjw in  19.7.30
         * 同时验证header和body里的accesstoken
         * (empty($param['access_token']) || $param['access_token'] != get_access_token()) && $this->apiError(CodeBase::$accessTokenError);
         */
        // dump($param); die;
        // $access_token = isset($_SERVER['HTTP_ACCESSTOKEN'])?$_SERVER['HTTP_ACCESSTOKEN']:$param['accesstoken'];

        // (empty($access_token) || $access_token != get_access_token()) && $this->apiError(CodeBase::$accessTokenError);
        $whiteList = $this->whiteList();

        if(!in_array(URL,$whiteList)){
            $access_token = isset($_SERVER['HTTP_ACCESSTOKEN'])?$_SERVER['HTTP_ACCESSTOKEN']:$param['accesstoken'];

            (empty($access_token) || $access_token != get_access_token()) && $this->apiError(CodeBase::$accessTokenError);
        }
        
        /**
         * edit by fjw in 19.7.30
         * 参数不为空，接口加解密不为空，则对传输的数据进行解密操作
         * 
         */
        if(!empty($param) && !empty($info['encode_type'])){

            switch($info['encode_type']){
                case 'rsa':
                    $param = rsa_decrypt($param['data']);
                break;
                case 'aes':
                    $param = aes_decrypt($param['data']);
                break;
                default: break;
            }

            if($param == false) $this->apiError(CodeBase::$dataDecryptError);

            $json_decode_param = json_decode($param, true); // 

            $param = ($json_decode_param == null) ? $param : $json_decode_param;

        }

        if ($info['is_user_token'] && empty($param['user_token'])) {
            
            $this->apiError(CodeBase::$userTokenNull);
            
        } elseif ($info['is_user_token']) {
        
            $decoded_user_token = decoded_user_token($param['user_token']);
            
            is_string($decoded_user_token) && $this->apiError(CodeBase::$userTokenError);
        }
        
        $info['is_request_sign']    && (empty($param['data_sign'])      || create_sign_filter($param) != $param['data_sign']) && $this->apiError(CodeBase::$dataSignError);
    
        return $param;
    }
}
