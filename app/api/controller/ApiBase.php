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

use app\common\controller\ControllerBase;
use think\Hook;

/**
 * 接口基类控制器
 */
class ApiBase extends ControllerBase
{
    
    /**
     * 基类初始化
     */
    public function __construct()
    {
        
        parent::__construct();
        // fqm 19.10.23
        // $this->param = $this->logicApiBase->checkParam($this->param);

        // add by fjw in 19.7.31: 如果存在user_token，则使用jwt解密 
        // $this->param['decoded_user_token'] = !empty($this->param['user_token'])?decoded_user_token($this->param['user_token'])['data']:[];

        // 接口控制器钩子
        Hook::listen('hook_controller_api_base', $this->request);
        
        debug('api_begin');
    }
    
    /**
     * API返回数据
     */
    public function apiReturn($code_data = [], $return_data = [], $return_type = 'json')
    {
        
        debug('api_end');
        
        $result = $this->logicApiBase->apiReturn($code_data, $return_data, $return_type);
        
        return $result;
    }
}
