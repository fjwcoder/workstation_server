<?php
/**
 * Users 用户验证器
 */

namespace app\common\validate;

class Users extends ValidateBase
{

    // 验证规则
    protected $rule = [
        'UserName'  => 'require|alphaNum|unique:users',
        'md5_password'  => 'require|length:6,12',
        'Name'  => 'require',
        // 'EmailAddress'  => 'require|email',
        'IsActive'  => 'require|in:0,1',
        'auth_id'   => 'require|number',
    ];

    // 验证提示
    protected $message = [
        'UserName.require'    => '请输入账号',
        'UserName.unique'    => '账号已存在',
        'UserName.alphaNum' =>'账号只能是字符或数字',
        'md5_password.require'    => '请输入密码',
        'md5_password.length'    => '密码长度为6-12位',
        'Name.require'    => '请输入昵称',
        // 'EmailAddress.require'    => '请输入邮箱',
        // 'EmailAddress.email'    => '邮箱格式不正确',
        'IsActive.require'    => '请选择是否启用',
        'IsActive.in'    => '请重新选择是否启用',
        'auth_id.require' =>'请选择用户权限',
        'auth_id.number' =>'用户权限不正确',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['UserName','md5_password','Name','IsActive','auth_id'],
        'edit'  =>  ['Name','IsActive','auth_id'],
        'editpwd'  =>  ['md5_password'],
    ];



}