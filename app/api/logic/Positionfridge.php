<?php
/*
 * @Descripttion: Positionfridge 逻辑层
 * @Author: fqm
 * @Date: 2019-08-21 13:38:52
 */

namespace app\api\logic;
use app\api\error\Common as CommonError;
use think\Db;

class Positionfridge extends ApiBase
{

    /**
     * 冰箱注册
     * @return unique_code 注册成功后返回RSA加密的unique_code
     */
    public function regist($param = [])
    {
        $validate_result = $this->validatePositionFridge->check($param);
        
        if(!$validate_result){
            return [API_CODE_NAME => 40010, API_MSG_NAME => $this->validatePositionFridge->getError()];
        }

        // mac地址
        $mac_address = !empty($param['mac_address']) ? $param['mac_address'] : '';
        // 接种点id
        $position_id = !empty($param['position_id']) ? $param['position_id'] : '';
        // 当前时间 毫秒
        $microtime = microtime(true);
        // unique_code mac地址+接种点ID+微秒时间戳
        $unique_code = md5($mac_address . $position_id . $microtime);

        $data = [
            'position_id'       => $position_id,
            'unique_code'       => $unique_code,
            'mac_address'       => $mac_address,
            'create_time'       => time(),
            'status'            => 1, // 注册后状态
        ];

        Db::startTrans();
        try{
            $fridge_id = Db::name('m_position_fridge')->insertGetId($data);
            $p_o_data = [
                'position_id' => $position_id,
                'fridge_id'   => $fridge_id,
                'user_type'   => !empty($param['user_type']) ? $param['user_type'] : 8, //用户类型：9 预设超级管理员；8 管理员 1 普通操作员 不传默认为管理员
                'name'        => $param['name'],
                'mobile'      => $param['mobile'],
                'password'    => md5($param['password']),
                'id_card'     => $param['id_card'], //身份证号
                'create_time' => time(),
            ];

            Db::name('m_position_operator')->insert($p_o_data);

            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            return CommonError::$editFailed;
        }

        // 冰箱注册成功后，返回 RSA 私钥加密的 unique_code
        return ['unique_code' => rsa_encrypt($unique_code,'pri')];


    }




}