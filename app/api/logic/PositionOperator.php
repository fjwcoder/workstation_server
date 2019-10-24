<?php
/*
 * @Descripttion: Positionoperator 逻辑层
 * @Author: fqm
 * @Date: 2019-08-22 15:48:15
 */

namespace app\api\logic;
use app\api\error\Common as CommonError;

class Positionoperator extends ApiBase
{


    /**
     * 验证是不是管理员
     * add by fqm in 19.9.6
     */
    public function checkAdmin($id, $mobile, $user_id = 0)
    {
        // 当前管理员信息
        $adminInfo = $this->modelMPositionOperator->getInfo(['mobile'=>$mobile,'id'=>$id],'user_type,position_id');

        // 判断当前管理员有没有权限 
        if($adminInfo['user_type'] == 8){
            
            // 判断当前需要操作的操作员是不是和当前管理员一个接种点
            $userInfo = $this->modelMPositionOperator->getInfo(['id'=>$user_id],'position_id');
        
            !empty($user_id) ? $position_id = $userInfo['position_id'] : $position_id = 0;
            
            if($position_id == 0 || $adminInfo['position_id'] !== $position_id ){
                return CommonError::$noPrivileges;
            }else{
                return 1;
            }
        }elseif($adminInfo['user_type'] == 9){
            return 1;
        }else{
            return CommonError::$noPrivileges;
        }

    }


    /**
     * 添加操作人员
     * edit by fqm in 19.9.2 缩短部分传输字段键名
     */
    public function addOperator($param = [])
    {
        // add by fqm in 19.9.9 添加操作员时进行验证
        $validate_result = $this->validatePositionFridge->check($param);
        
        if(!$validate_result){
            return [API_CODE_NAME => 40010, API_MSG_NAME => $this->validatePositionFridge->getError()];
        }

        $unique_code = rsa_decrypt($param['uc'],'pub');
        // 判断是不是当前接种点的管理员 不是：提示没有权限 是：添加操作员
        // 不传接种点ID 通过冰箱 unique_code 获取当前冰箱的接种点
        $fridgeInfo = $this->modelMPositionFridge->getInfo(['unique_code'=>$unique_code]);
        
        $where = [
            'position_id' => $fridgeInfo['position_id'],
            'id' => $param['o_id'],
        ];
        $userInfo = $this->modelMPositionOperator->getInfo($where);

        // 8:管理员 
        if($userInfo['user_type'] < 8){
            return CommonError::$noPrivileges;
        }
        
        $data = [
            'position_id' => $fridgeInfo['position_id'],
            'fridge_id' => $fridgeInfo['id'],
            'name' => !empty($param['name']) ? $param['name'] : '',
            'mobile' => !empty($param['mobile']) ? $param['mobile'] : '',
            'password' => !empty($param['mobile']) ? md5($param['password']) : md5(123456),
            'id_card' => $param['id_card'],
            'user_type' => 1, // 用户类型：9：预设超级管理员；8：管理员 1：普通操作员
        ];

        $result = $this->modelMPositionOperator->setInfo($data);
        // by fqm in 19.9.2 修改返回数据
        return $result ? true : CommonError::$editFailed;

    }

    /**
     * 本接种点操作人员列表
     * edit by fqm in 19.9.2 缩短部分传输字段键名
     */
    public function operatorList($param = [])
    {

        $userInfo = $this->modelMPositionOperator->getInfo(['mobile'=>$param['o_mobile'],'id'=>$param['o_id']]);

        // 8:管理员 
        if($userInfo['user_type'] < 8){
            return CommonError::$noPrivileges;
        }

        $unique_code = rsa_decrypt($param['uc'],'pub');

        $position_id = $this->modelMPositionFridge->getValue(['unique_code'=>$unique_code],'position_id');

        $where = [
            'position_id' => $position_id,
            'status' => 1,
        ];

        $field = 'id, position_id, fridge_id, user_type, name, mobile, create_time, update_time';

        $operators = $this->modelMPositionOperator->getList($where, $field,'id desc',false);

        return $operators;
    }

    /**
     * 编辑操作人员
     * edit by fqm in 19.9.2 缩短部分传输字段键名
     * 编辑数据时，判断不传值默认为不修改
     */
    public function editOperator($param = [])
    {
        // 验证管理员权限
        $status = $this->checkAdmin($param['o_id'],$param['o_mobile'],$param['user_id']);
        if(is_array($status)){
            return $status;
        }
        
        $unique_code = rsa_decrypt($param['uc'],'pub');

        $fridgeInfo = $this->modelMPositionFridge->getInfo(['unique_code'=>$unique_code]);
        
        $optionInfo = $this->modelMPositionOperator->getInfo(['id'=>$param['user_id']]);

        $data = [
            'id' => $param['user_id'],
            'position_id' => $fridgeInfo['position_id'],
            'fridge_id' => $fridgeInfo['id'],
            'name' => !empty($param['name']) ? $param['name'] : $optionInfo['name'],
            'mobile' => !empty($param['mobile']) ? $param['mobile'] : $optionInfo['mobile'],
            'password' => !empty($param['pwd']) ? md5($param['pwd']) : $optionInfo['password'],
            'id_card' => !empty($param['id_card']) ? $param['id_card'] : $optionInfo['id_card'],
            // 'user_type' => 1, // 用户类型：9：预设超级管理员；8：管理员 1：普通操作员
        ];

        $result = $this->modelMPositionOperator->setInfo($data);
        // by fqm in 19.9.2 修改返回数据
        return $result ? true : CommonError::$editFailed;

    }

    /**
     * 删除操作人员
     * edit by fqm in 19.9.2 缩短部分传输字段键名
     */
    public function delOperator($param = [])
    {
        // 验证管理员权限
        $status = $this->checkAdmin($param['o_id'],$param['o_mobile'],$param['user_id']);
        if(is_array($status)){
            return $status;
        }

        $result = $this->modelMPositionOperator->deleteInfo(['id'=>$param['user_id']]);
        // by fqm in 19.9.2 修改返回数据
        return $result ? true : CommonError::$editFailed;

    }

    /**
     * 获取操作人员详情
     * add by fqm in 19.9.6 
     */
    public function getOperator($param = [])
    {
        // 验证管理员权限
        $status = $this->checkAdmin($param['o_id'],$param['o_mobile'],$param['user_id']);
        if(is_array($status)){
            return $status;
        }

        $result = $this->modelMPositionOperator->getInfo(['id'=>$param['user_id']]);
        // by fqm in 19.9.2 修改返回数据
        return $result ? $result : CommonError::$editFailed;
    }




}