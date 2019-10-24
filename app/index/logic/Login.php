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
 * 登录逻辑
 */
class Login extends IndexBase
{
    
    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {
        
        $validate_result = $this->validateLogin->scene('index')->check(compact('username','password'));
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateLogin->getError()];
        }
        
        $user = $this->logicUser->getUserInfo(['username' => $username]);

        if (!empty($user['md5_password']) && md5($password) == $user['md5_password']) {
            
            // $this->logicUser->setUserValue(['id' => $user['id']], TIME_UT_NAME, TIME_NOW);

            $auth = ['user_id' => $user['Id'], TIME_UT_NAME => TIME_NOW];

            session('user_info', $user);
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));

            // action_log('登录', '工作站登录操作，username：'. $username);

            return [RESULT_REDIRECT, '登录成功', url('index/index')];
            
        } else {
            
            $error = empty($user['Id']) ? '用户账号不存在' : '密码输入错误';
            
            return [RESULT_ERROR, $error];
        }
    }
    
    /**
     * 注销当前用户
     */
    public function logout()
    {
        
        clear_login_session();
        
        return [RESULT_SUCCESS, '注销成功', url('login/index')];
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
