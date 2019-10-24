<?php
/*
 * @Descripttion: Vaccine验证器
 * @Author: fqm
 * @Date: 2019-08-20 13:37:36
 */

namespace app\admin\validate;

class Vaccine extends AdminBase
{
    // 验证规则
    protected $rule =   [
        
        'zh_name_abbr'  => 'require',
        'zh_name_full'  => 'require',
        'inject_date'   => 'require',
        'inject_week'   => 'require',
        'category'      => 'require|number|in:1,2'
    ];

    // 验证提示
    protected $message  =   [
        
        'zh_name_abbr.require'      => '中文简称不能为空',
        'zh_name_full.require'      => '中文全称不能为空',
        'inject_date.require'       => '接种时间不能为空',
        'inject_week.require'       => '出生后第几周接种不能为空',
        'category.require'          => '是否免费不能为空',
        'category.number'           => '是否免费格式不正确',
        'category.in'               => '是否免费格式不正确',
    ];
}