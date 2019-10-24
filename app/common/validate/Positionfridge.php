<?php
/*
 * @Descripttion: Positionfridge 验证器
 * @Author: fqm
 * @Date: 2019-08-21 14:14:22
 */

namespace app\common\validate;

class PositionFridge extends ValidateBase
{
    // 验证规则
    protected $rule =   [
        'name'              => 'require|chsAlpha',
        'password'          => 'require',
        'mobile'            => 'require|is_moblie|unique:m_position_operator',
        'id_card'           => 'require|is_id_card|unique:m_position_operator',
        'position_id'       => 'require|gt:0',
    ];

    // 验证提示
    protected $message  =   [
        'name.require'              => '姓名不能为空',
        'name.chsAlpha'             => '姓名格式不正确',
        'password.require'          => '密码不能为空',
        'mobile.require'            => '手机号不能为空',
        'mobile.is_moblie'          => '手机号格式不正确',
        'mobile.unique'             => '手机号已存在',
        'id_card.require'           => '身份证号不能为空',
        'id_card.is_id_card'        => '身份证号格式不正确',
        'id_card.unique'            => '身份证号已存在',
        'position_id.require'       =>'请刷新页面重新提交',
        'position_id.gt'            =>'请刷新页面重新提交'
    ];

    // 验证手机号
    protected function is_moblie($phone)
    {
        $re_rule = "/^1[34578]{1}\d{9}$/";

        if(preg_match($re_rule,$phone)){
            return true;
        }else{
            return false;
        }
    }

    // 验证身份证号
    protected function is_id_card($id_card)
    {
        $vCity = array(
            '11','12','13','14','15','21','22',
            '23','31','32','33','34','35','36',
            '37','41','42','43','44','45','46',
            '50','51','52','53','54','61','62',
            '63','64','65','71','81','82','91'
        );
        
        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $id_card)) return false;
        
        if (!in_array(substr($id_card, 0, 2), $vCity)) return false;
        
        $id_card = preg_replace('/[xX]$/i', 'a', $id_card);
        
        $vLength = strlen($id_card);
        
        if($vLength == 18){
            $vBirthday = substr($id_card, 6, 4) . '-' . substr($id_card, 10, 2) . '-' . substr($id_card, 12, 2);
        } else {
            $vBirthday = '19' . substr($id_card, 6, 2) . '-' . substr($id_card, 8, 2) . '-' . substr($id_card, 10, 2);
        }
        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
        if ($vLength == 18) {
            $vSum = 0;
            for ($i = 17 ; $i >= 0 ; $i--) {
                $vSubStr = substr($id_card, 17 - $i, 1);
        
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
            }
            if($vSum % 11 != 1) return false;
        }
        
        return true;
    }

}
