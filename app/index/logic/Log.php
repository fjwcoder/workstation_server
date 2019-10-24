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

/**
 * 行为日志逻辑
 */
class Log extends IndexBase
{
    
    /**
     * 获取日志列表
     */
    public function getLogList()
    {
        
        $sub_user_ids = $this->logicUser->getSubUserIds(MEMBER_ID);
        
        $where = [];
        
        $sub_user_ids[] = MEMBER_ID;
        
        !IS_ROOT && $where['user_id'] = ['in', $sub_user_ids];
        
        return $this->modelUserActionLog->getList($where, true, 'create_time desc');
    }
  
    /**
     * 日志删除
     */
    public function logDel($where = [])
    {
        
        return $this->modelUserActionLog->deleteInfo($where) ? [RESULT_SUCCESS, '日志删除成功'] : [RESULT_ERROR, $this->modelUserActionLog->getError()];
    }
    
    /**
     * 日志添加
     */
    public function logAdd($name = '', $describe = '')
    {
        
        $user_info = session('user_info');
        
        $request = request();
        
        $data['user_id'] = $user_info['id'];
        $data['username']  = $user_info['username'];
        $data['ip']        = $request->ip();
        $data['url']       = $request->url();
        $data['status']    = DATA_NORMAL;
        $data['name']      = $name;
        $data['describe']  = $describe;
        
        $url = url('logList');
        
        $this->modelUserActionLog->is_update_cache_version = false;
        
        return $this->modelUserActionLog->setInfo($data) ? [RESULT_SUCCESS, '日志添加成功', $url] : [RESULT_ERROR, $this->modelUserActionLog->getError()];
    }
}
