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

namespace app\index\controller;

use app\common\controller\ControllerBase;
use think\Hook;

/**
 * 前端模块基类控制器
 */
class IndexBase extends ControllerBase
{
    public $uid = 0;
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        // 执行父类构造方法
        parent::__construct();

        /**
         * add by fjw in 19.9.28
         * 判断是否登录
         */
        // $uid = user_is_login();
        // if($uid === 0){
        //     return $this->redirect('/index/login/index');
        // }

        // 前台控制器钩子
        Hook::listen('hook_controller_index_base', $this->request);
    }
}
