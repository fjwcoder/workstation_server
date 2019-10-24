<?php
/*
 * @Descripttion: Injectposition验证器
 * @Author: fqm
 * @Date: 2019-08-19 15:43:58
 */

namespace app\common\validate;

class InjectPosition extends ValidateBase
{
    
    // 验证规则
    protected $rule =   [
        'account'          => 'require|unique:m_inject_position',
        'password'         => 'require',
        'repassword'       => 'require|confirm:password',
        'province'         => 'require',
        'address'          => 'require',
        'phone'            => 'require',
        'name'             => 'require',
    ];

    // 验证提示
    protected $message  =   [
        'account.require'         => '账号不能为空',
        'account.unique'          => '账号已存在，请重新输入',
        'password.require'        => '密码不能为空',
        'repassword.require'      => '确认密码不能为空',
        'repassword.confirm'      => '两次密码不一致，请重新输入',
        'province.require'        => '城市不能为空',
        'address.require'         => '详细地址不能为空',
        'phone.require'           => '联系电话不能为空',
        'name.require'            => '接种点名称不能为空',

    ];
}