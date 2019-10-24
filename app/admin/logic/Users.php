<?php
/**
 * Users 用户逻辑层
 */

namespace app\admin\logic;

class Users extends AdminBase
{


    /**
     * 获取用户列表
     */
    public function getUserList($where, $field = true)
    {

        // $where['IsActive'] = 1;
        
        $userList = $this->modelUsers->getList($where, $field, 'UserName asc', 15);

        return $userList;

    }


    /**
     * 禁用用户
     */
    public function isActiveUser($param = [])
    {

        if(empty($param['Id'])) return ['code'=>400,'msg'=>'操作失败'];

        $Id = $param['Id'];

        if (SYS_ADMINISTRATOR_ID == $Id || MEMBER_ID == $Id) {
            
            // return [RESULT_ERROR, '天神和自己不能禁用哦~',];
            return ['code'=>400,'msg'=>'天神和自己不能禁用哦~'];
        }

        $userInfo = $this->modelUsers->getInfo(['Id'=>$Id]);

        if($userInfo['IsActive'] == 1){
            $isActive = 0;
        }else{
            $isActive = 1;
        }

        $result = $this->modelUsers->updateInfo(['Id'=>$Id], ['IsActive'=>$isActive]);

        return $result ? ['code'=>200,'msg'=>'操作成功'] : ['code'=>400,'msg'=>$this->modelUsers->getError()];
        // return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $this->modelUsers->getError()];
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($where = [], $field = true)
    {

        $userInfo = $this->modelUsers->getInfo($where, $field);

        if(!empty($userInfo['Id'])){
            $userInfo['id'] = $userInfo['Id'];
            $userInfo['nickname'] = $userInfo['Name'];
        }

        return $userInfo;

    }

    /**
     * 修改用户信息
     */
    public function editUserInfo($param = [])
    {
        $validate_result = $this->validateUsers->scene('edit')->check($param);
        
        if (!$validate_result) {
            return ['code'=>400,'msg'=> $this->validateUsers->getError()];
        }

        // dump($param);die;
        $data = [
            'Id'=>$param['Id'],
            'UserName'=>$param['UserName'],
            'Name'=>$param['Name'],
            'EmailAddress'=>$param['EmailAddress'],
            'IsActive'=>$param['IsActive'],
            'NormalizedUserName'=>strtoupper($param['UserName']),
            'NormalizedEmailAddress'=>strtoupper($param['EmailAddress']),
        ];

        $result = $this->modelUsers->setInfo($data);

        return $result ? ['code'=>200, 'msg'=>'操作成功'] :['code'=>0, 'msg'=>'操作失败'];
    }

    /**
     * 重置用户密码
     */
    public function editUserPwd($param = [])
    {
        if(empty($param['md5_password'])){
            return ['code'=>400, 'msg'=>'请输入新密码'];
        }

        $data = [
            'Id'=>$param['Id'],
            'md5_password'=>md5($param['md5_password']),
        ];

        $result = $this->modelUsers->setInfo($data);

        return $result ? ['code'=>200, 'msg'=>'操作成功'] : ['code'=>400, 'msg'=>'操作失败'];
    }

    /**
     * 创建用户
     */
    public function addUserInfo($param = [])
    {
        // dump($param);die;

        $validate_result = $this->validateUsers->scene('add')->check($param);
        
        if (!$validate_result) {
            return ['code'=>400,'msg'=> $this->validateUsers->getError()];
        }

        $time = date("Y-m-d H:i:s");

        $uId = session('member_info')['id'];

        $data = [
            'UserName'=>$param['UserName'],
            'md5_password'=>md5($param['md5_password']),
            'Name'=>$param['Name'],
            'EmailAddress'=>$param['EmailAddress'],
            'IsActive'=>$param['IsActive'],
            'CreationTime'=>$time,
            'IsDeleted'=>0,
            'Surname'=>$param['Name'],
            'Password'=>md5($param['md5_password']),
            'AccessFailedCount'=>0,
            'IsLockoutEnabled'=>1,
            'IsPhoneNumberConfirmed'=>0,
            'IsTwoFactorEnabled'=>0,
            'IsEmailConfirmed'=>0,
            'NormalizedUserName'=>strtoupper($param['UserName']),
            'NormalizedEmailAddress'=>strtoupper($param['EmailAddress']),

        ];

        $result = $this->modelUsers->setInfo($data);

        return $result ? ['code'=>200, 'msg'=>'操作成功'] : ['code'=>400, 'msg'=>'操作失败'];
    }



    /**
     * 设置会员信息
     */
    public function setUserValue($where = [], $field = '', $value = '')
    {
        return $this->modelUsers->setFieldValue($where, $field, $value);
    }
}