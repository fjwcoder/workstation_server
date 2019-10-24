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

namespace app\common\validate;

/**
 * 配置验证器
 */
class Config extends ValidateBase
{
    
    // 验证规则
    protected $rule =   [
        'name'          => 'require|unique:config',
        'title'         => 'require',
    ];

    // 验证提示
    protected $message  =   [
        'name.require'         => '标识不能为空',
        'name.unique'          => '标识已经存在',
        'title.require'        => '名称不能为空',
    ];
}
