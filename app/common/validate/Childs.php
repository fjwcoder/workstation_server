<?php
/**
 * Childs 儿童信息验证器
 */

namespace app\common\validate;

class Childs extends ValidateBase
{


    // 验证规则
    protected $rule = [
        'CardNo'  => 'require',
        'Name'  => 'require',
        'Sex'     => 'require|in:1,2',
        'BirthDate' => 'require|date',
        'ParentName' => 'require',
        'Address' => 'require',
        'Mobile' => 'require|number',
    ];

    // 验证提示
    protected $message = [
        'CardNo.require'    => '请输入卡号',
        // 'CardNo.unique'    => '卡号已存在',
        'Name.require'    => '请填写儿童姓名',
        'Sex.require'    => '请选择儿童性别',
        'Sex.in'    => '性别格式不正确',
        'BirthDate.require'    => '请选择出生日期',
        'BirthDate.date'    => '出生日期格式不正确',
        'Address.require'    => '请填写家庭住址',
        'ParentName.require'    => '请填写家长姓名',
        'Mobile.require'    => '请填写联系电话',
        'Mobile.number'    => '联系电话格式不正确',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['CardNo','Name','Sex','BirthDate','ParentName','Address','Mobile'],
    ];




}