<?php
/**
 * Settings 验证器
 */

namespace app\common\validate;

class Settings extends ValidateBase
{

    // 验证规则
    protected $rule = [
        'account'  => 'require',
        'password' => 'require',
        'inject_position_type' => 'require|in:1,2',
        'province' => 'require|number',
        'city' => 'require|number',
        'district' => 'require|number',
        'address' => 'require',
        'phone' => 'require',
    ];

    // 验证提示
    protected $message = [
        'account.require'    => '请先选择管理员',
        'password.require'    => '请先选择管理员',
        'inject_position_type.require'   => '请先选择接种点类型',
        'inject_position_type.in'    => '请刷新页面重新选择接种点类型',
        'province.require'    => '请先选择省市区',
        'province.number'    => '请刷新页面重新选择省市区',
        'city.require'    => '请先选择省市区',
        'city.number'    => '请刷新页面重新选择省市区',
        'district.require'    => '请先选择省市区',
        'district.number'    => '请刷新页面重新选择省市区',
        'address.require'    => '请填写详细地址',
        'phone.require'    => '请先填写接种点电话',
    ];

    // 应用场景
    protected $scene = [
        'set'  =>  ['account','password','inject_position_type','province','city','district','address','phone'],
    ];




}