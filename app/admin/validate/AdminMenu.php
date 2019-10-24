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

namespace app\admin\validate;

/**
 * 菜单验证器
 */
class AdminMenu extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name'  => 'require',
        'sort'  => 'require|number',
        'url'   => 'require|unique:admin_menu'
    ];

    // 验证提示
    protected $message  =   [
        
        'name.require'    => '菜单不能为空',
        'sort.require'    => '排序值不能为空',
        'url.require'     => 'url不能为空',
        'url.unique'      => 'url已存在',
        'sort.number'     => '排序值必须为数字',
    ];

    // 应用场景
    protected $scene = [
        
        'add'  =>  ['name', 'sort', 'url'],
        'edit' =>  ['name', 'sort', 'url'],
    ];
    
}
