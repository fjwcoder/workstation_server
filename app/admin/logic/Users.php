<?php
/**
 * Users 用户逻辑层
 */

namespace app\admin\logic;
use think\Db;

class Users extends AdminBase
{


    /**
     * 获取用户列表
     */
    public function getUserList($where, $field = true)
    {

        // $where['IsActive'] = 1;
        $this->modelUsers->alias('u');

        $this->modelUsers->join = [
            [SYS_DB_PREFIX . 'auth_group_access a', 'u.Id = a.member_id', 'LEFT'],
        ];

        
        $userList = $this->modelUsers->getList($where, $field, 'u.UserName asc', 15);

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

        // 启动事务
        Db::startTrans();
        try{

            // 查询出原用户名
            $userName = Db::name('users')->where(['Id'=>$Id])->value('UserName');

            $data = [
                'Id'=>$Id,
                'UserName'=>$userName,
                'IsActive'=>$isActive,
            ];

            Db::name('users')->where(['Id'=>$Id])->update(['IsActive'=>$isActive]);

            $return = $this->postUserInfo($data);
            if(is_array($return)){
                // 回滚事务
                Db::rollback();
                return $return;
            }
            if($return === true){
                // 提交事务
                Db::commit(); 
                $result = true;
            }else{
                // 回滚事务
                Db::rollback();
                $result = false;
            }
               
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $result = false;
        }

        return $result ? ['code'=>200, 'msg'=>'操作成功'] : ['code'=>400, 'msg'=>'操作失败'];

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

        $result = false;

        // 启动事务
        Db::startTrans();
        try{

            $data = [
                'Id'=>$param['Id'],
                // 禁止修改用户名
                // 'UserName'=>$param['UserName'],
                'Name'=>$param['Name'],
                // 'EmailAddress'=>$param['EmailAddress'],
                'IsActive'=>$param['IsActive'],
                // 'NormalizedUserName'=>strtoupper($param['UserName']),
                // 'NormalizedEmailAddress'=>strtoupper($param['EmailAddress']),
            ];

            Db::name('users')->where(['Id'=>$param['Id']])->update($data);

            $authGroupInfo = [
                'member_id'=>$param['Id'],
                'group_id'=>$param['auth_id'],
                'status'=>1
            ];

            $userGroupAccess = Db::name('auth_group_access')->where(['member_id'=>$param['Id']])->find();

            if($userGroupAccess){
                Db::name('auth_group_access')->where(['member_id'=>$param['Id']])->update($authGroupInfo);
            }else{
                Db::name('auth_group_access')->insert($authGroupInfo);
            }
            // 查询出原用户名
            $userName = Db::name('users')->where(['Id'=>$param['Id']])->value('UserName');
            $param['UserName'] = $userName;

            $return = $this->postUserInfo($param);
            if(is_array($return)){
                // 回滚事务
                Db::rollback();
                return $return;
            }
            if($return === true){
                // 提交事务
                Db::commit(); 
                $result = true;
            }else{
                // 回滚事务
                Db::rollback();
                $result = false;
            }
               
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $result = false;
        }

        return $result ? ['code'=>200, 'msg'=>'操作成功'] : ['code'=>400, 'msg'=>'操作失败'];
    }

    /**
     * 重置用户密码
     */
    public function editUserPwd($param = [])
    {

        $validate_result = $this->validateUsers->scene('editpwd')->check($param);
        
        if (!$validate_result) {
            return ['code'=>400,'msg'=> $this->validateUsers->getError()];
        }

        $Id = $param['Id'];

        $userInfo = $this->modelUsers->getInfo(['Id'=>$Id]);

        $result = false;

        // 启动事务
        Db::startTrans();
        try{

            $data = [
                'md5_password'=>md5($param['md5_password']),
            ];

            Db::name('users')->where(['Id'=>$param['Id']])->update($data);
            $userName = Db::name('users')->where(['Id'=>$param['Id']])->value('UserName');

            $uData = [
                // 禁止修改用户名
                'UserName'=>$userName,
                'md5_password'=>$data['md5_password'],
            ];

            $return = $this->postUserInfo($uData);

            if(is_array($return)){
                // 回滚事务
                Db::rollback();
                return $return;
            }
            if($return === true){
                // 提交事务
                Db::commit(); 
                $result = true;
            }else{
                // 回滚事务
                Db::rollback();
                $result = false;
            }
               
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $result = false;
        }

        return $result ? ['code'=>200, 'msg'=>'操作成功'] : ['code'=>400, 'msg'=>'操作失败'];
    }

    /**
     * 创建用户
     */
    public function addUserInfo($param = [])
    {

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
            // 'EmailAddress'=>$param['EmailAddress'],
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
            // 'NormalizedEmailAddress'=>strtoupper($param['EmailAddress']),

        ];

        $result = false;

        // 启动事务
        Db::startTrans();
        try{
            $uId = Db::name('users')->insertGetId($data);

            $authGroupInfo = [
                'member_id'=>$uId,
                'group_id'=>$param['auth_id'],
                'status'=>1
            ];

            $userGroupAccess = Db::name('auth_group_access')->where(['member_id'=>$uId])->find();

            if($userGroupAccess){
                Db::name('auth_group_access')->where(['member_id'=>$uId])->update($authGroupInfo);
            }else{
                Db::name('auth_group_access')->insert($authGroupInfo);
            }

            $return = $this->postUserInfo($param);

            if(is_array($return)){
                // 回滚事务
                Db::rollback();
                return $return;
            }
            if($return === true){
                // 提交事务
                Db::commit(); 
                $result = true;
            }else{
                // 回滚事务
                Db::rollback();
                $result = false;
            }
               
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $result = false;
        }

        return $result ? ['code'=>200, 'msg'=>'操作成功'] : ['code'=>400, 'msg'=>'操作失败'];
    }



    /**
     * 设置会员信息
     */
    public function setUserValue($where = [], $field = '', $value = '')
    {
        return $this->modelUsers->setFieldValue($where, $field, $value);
    }


    /**
     * 添加/修改用户信息后，同时更新到服务端
     * add by fqm in 19.10.30
     */
    public function postUserInfo($param)
    {

        // 当前接种点id
        $positionId = $this->modelSettings->getValue(['Name'=>'App.InjectPositionId'],'Value');

        if(empty($positionId)) return ['code'=>0,'msg'=>'请先注册接种点'];

        $data = [
            'position_id'=>$positionId,
        ];

        !empty($param['auth_id']) ? $data['user_type']=$param['auth_id'] : '';
        !empty($param['UserName']) ? $data['mobile']=$param['UserName'] : '';
        !empty($param['md5_password']) ? $data['password']=md5($param['md5_password']) : '';
        !empty($param['IsActive']) ? $data['status']=$param['IsActive'] : '';

        // dump($data);die;

        $refrigeratorUrl = $this->modelSettings->getValue(['Name'=>'App.refrigeratorUrl'], 'Value');

        $result = httpsPost($refrigeratorUrl . '/setuserinfo', $data);

        $result = json_decode($result, true);

        if($result['code'] == 200){
            return true;
        }else{
            return false;
        }

    }
}