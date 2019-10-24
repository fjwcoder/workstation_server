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

namespace app\api\validate;

/**
 * 会员验证器
 */
class Member extends ApiBase
{
    
    // 验证规则
    protected $rule = [
        'username'  => 'require',
        'password'  => 'require',
    ];

    // 验证提示
    protected $message = [
        'username.require'    => '用户名不能为空',
        'password.require'    => '密码不能为空',
    ];

    // 应用场景
    protected $scene = [
        
        'login'  =>  ['username','password'],
    ];
}
