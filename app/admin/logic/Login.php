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

namespace app\admin\logic;

/**
 * 登录逻辑
 */
class Login extends AdminBase
{
    
    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {
        
        $validate_result = $this->validateLogin->scene('admin')->check(compact('username','password','verify'));
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateLogin->getError()];
        }
        
        $member = $this->logicUsers->getUserInfo(['UserName' => $username]);

        if (!empty($member['md5_password']) && md5($password) == $member['md5_password']) {

            $today = date("Y-m-d H:i:s");

            $this->logicUsers->setUserValue(['Id' => $member['id']], 'LastLoginTime', $today);

            $auth = ['member_id' => $member['id'], 'LastLoginTime' => $today];

            session('member_info', $member);
            session('member_auth', $auth);
            session('member_auth_sign', data_auth_sign($auth));

            // action_log('登录', '登录操作，UserName：'. $username);

            // 添加访问记录
            $logData = [
                'UserId' => $member['id'],
                'UserNameOrEmailAddress' => $member['UserName'],
                
                'Result' => 1,
            ];

            $this->logicUserloginattempts->addSingLog($logData);

            return [RESULT_SUCCESS, '登录成功', url('index/index')];
            
        } else {

            if(!empty($member['md5_password'])){
                $logData = [
                    'UserId' => $member['id'],
                    'UserNameOrEmailAddress' => $member['UserName'],
                    'Result' => 3,
                ];
            }else{
                $logData = [
                    // 'UserId' => $member['id'],
                    'UserNameOrEmailAddress' => $username,
                    'Result' => 2,
                ];
            }

            $this->logicUserloginattempts->addSingLog($logData);
            
            $error = empty($member['id']) ? '用户账号不存在' : '密码输入错误';
            
            return [RESULT_ERROR, $error];
        }
    }
    
    /**
     * 注销当前用户
     */
    public function logout()
    {
        
        clear_login_session();
        
        return [RESULT_SUCCESS, '注销成功', url('login/login')];
    }
    
    /**
     * 清理缓存
     */
    public function clearCache()
    {
        
        \think\Cache::clear();
        
        return [RESULT_SUCCESS, '清理成功', url('index/index')];
    }
}
