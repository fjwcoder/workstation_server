<?php
/**
 * Users 用户验证器
 */

namespace app\common\validate;

class Users extends ValidateBase
{

    // 验证规则
    protected $rule = [
        'UserName'  => 'require|unique:users',
        'md5_password'  => 'require|length:6,12',
        'Name'  => 'require',
        'EmailAddress'  => 'require|email',
        'IsActive'  => 'require|in:0,1',
    ];

    // 验证提示
    protected $message = [
        'UserName.require'    => '请输入账号',
        'UserName.unique'    => '账号已存在',
        'md5_password.require'    => '请输入密码',
        'md5_password.length'    => '密码长度为6-12位',
        'Name.require'    => '请输入昵称',
        'EmailAddress.require'    => '请输入邮箱',
        'EmailAddress.email'    => '邮箱格式不正确',
        'IsActive.require'    => '请选择是否启用',
        'IsActive.in'    => '请重新选择是否启用',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['UserName','md5_password','Name','EmailAddress','IsActive'],
        'edit'  =>  ['UserName','Name','EmailAddress','IsActive'],
    ];



}