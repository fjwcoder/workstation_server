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

namespace app\common\model;

use think\Cache;
/**
 * 接口模型
 */
class Api extends ModelBase
{
    private $api_cache_key = 'api_info';
    /**
     * add by fjw in 19.7.31
     * 向缓存中添加数据
     * 格式：key_name = [ 
     *      key_name = [],
     *      key_name = []
     * ]
     * @param time 默认永不过期
     */
    public function setCacheInfo($time = 0){
        $field = '
            api_url, id, request_type, is_request_data, 
            is_response_data, is_user_token, is_response_sign,
            is_request_sign, is_page, encode_type, status
        ';

        $api = self::where(['status'=>1]) -> column($field);

        // 缓存键名转为小写
        foreach($api as $k=>$v){
            $lower_k = strtolower($k);
            $api[$lower_k] = $v;
            unset($api[$k]);
        }

        $is_cache = Cache::set($this->api_cache_key, $api, $time);
        
        return ['api'=>$api, 'is_cache'=>$is_cache];
    }
    /**
     * add by fjw in 19.7.31
     * 从缓存中获取数据，不存在就查询后写入
     * 格式：key_name = [ 
     *      key_name = [],
     *      key_name = []
     * ]
     * @param rewrite 1: 强制重新写入
     * 
     */
    public function getCacheInfo($api_name = '', $rewrite = 0){


        if($rewrite == 1){ // 强制重新写入
            $api = $this->setCacheInfo()['api'];
        }else{
            $api = empty(Cache::get($this->api_cache_key))?$this->setCacheInfo()['api']:Cache::get($this->api_cache_key);
        }

        if(empty($api_name)){
            return $api;
        }else{
            $api_name = strtolower($api_name);
            if(isset($api[$api_name])){
                return $api[$api_name];
            }else{
                return null;
            }
        }
        
    }
    
    /**
     * 请求数据获取器
     */
    public function getRequestDataAttr()
    {
        
        return json_decode($this->data['request_data'], true);
    }
    
    /**
     * 响应数据获取器
     */
    public function getResponseDataAttr()
    {
        
        return json_decode($this->data['response_data'], true);
    }
    
    /**
     * API分组获取器
     */
    public function getGroupNameAttr()
    {
        
        return $this->modelApiGroup->getValue(['id' => $this->data['group_id']], 'name');
    }
    
    /**
     * 请求类型获取器
     */
    public function getRequestTypeTextAttr()
    {
        
        return $this->data['request_type'] ? 'GET' : 'POST';
    }
    
    /**
     * API状态获取器
     */
    public function getApiStatusTextAttr()
    {
        
        $array = parse_config_array('api_status_option');
        
        return $array[$this->data['api_status']];
    }
    
    /**
     * API研发者获取器
     */
    public function getDeveloperTextAttr()
    {
        
        $array = parse_config_array('team_developer');
        
        return $array[$this->data['developer']];
    }
}
