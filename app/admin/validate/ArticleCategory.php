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
 * 文章分类验证器
 */
class ArticleCategory extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        'name'          => 'require|unique:article_category',
    ];

    // 验证提示
    protected $message  =   [
        'name.require'         => '分类名称不能为空',
        'name.unique'          => '分类名称已经存在',
    ];
    
    // 应用场景
    protected $scene = [
        'edit'  =>  ['name'],
    ];
}
