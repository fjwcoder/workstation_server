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
 * 友情链接验证器
 */
class Blogroll extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name'              => 'require|unique:blogroll',
        'url'               => 'require',
        'sort'              => 'require|number',
    ];

    // 验证提示
    protected $message  =   [
        
        'name.require'              => '链接名称不能为空',
        'name.unique'               => '链接名称已存在',
        'url.require'               => '链接URL不能为空',
        'sort.require'              => '排序值不能为空',
        'sort.number'               => '排序值必须为数字'
    ];

    // 应用场景
    protected $scene = [
        
        'edit' =>  ['name','url','sort'],
    ];
    
}
