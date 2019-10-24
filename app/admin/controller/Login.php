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

namespace app\admin\controller;

use app\common\controller\ControllerBase;

/**
 * 登录控制器
 */
class Login extends ControllerBase
{
    
    /**
     * 登录
     */
    public function login()
    {
        
        member_is_login() && $this->jump(RESULT_REDIRECT, '已登录则跳过登录页', url('index/index'));
        
        // 关闭布局
        $this->view->engine->layout(false);
        
        return $this->fetch('login');
    }
    
    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {
        
        $this->jump($this->logicLogin->loginHandle($username, $password, $verify));
    }
    
    /**
     * 注销登录
     */
    public function logout()
    {
        
        $this->jump($this->logicLogin->logout());
    }
    
    /**
     * 清理缓存
     */
    public function clearCache()
    {
        
        $this->jump($this->logicLogin->clearCache());
    }
    
}
